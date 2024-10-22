<?php

namespace CeremonyCrmApp\Modules\Sales\Sales\Models;

use CeremonyCrmApp\Modules\Core\Customers\Models\Company;
use CeremonyCrmApp\Modules\Core\Customers\Models\Person;
use CeremonyCrmApp\Modules\Core\Settings\Models\Currency;
use CeremonyCrmApp\Modules\Core\Settings\Models\LeadStatus;
use CeremonyCrmApp\Modules\Core\Settings\Models\User;
use CeremonyCrmApp\Modules\Sales\Sales\Models\LeadHistory;
use CeremonyCrmApp\Modules\Sales\Sales\Models\LeadLabel;

class Lead extends \CeremonyCrmApp\Core\Model
{
  public string $table = 'leads';
  public string $eloquentClass = Eloquent\Lead::class;
  public ?string $lookupSqlValue = '{%TABLE%}.title';

  public array $relations = [
    'DEAL' => [ self::HAS_ONE, Deal::class, 'id_lead', 'id'],
    'COMPANY' => [ self::HAS_ONE, Company::class, 'id', 'id_company'],
    'USER' => [ self::BELONGS_TO, User::class, 'id_user', 'id'],
    'PERSON' => [ self::HAS_ONE, Person::class, 'id', 'id_person'],
    'STATUS' => [ self::HAS_ONE, LeadStatus::class, 'id', 'id_status'],
    'CURRENCY' => [ self::HAS_ONE, Currency::class, 'id', 'id_currency'],
    'HISTORY' => [ self::HAS_MANY, LeadHistory::class, 'id_lead', 'id', ],
    'LABELS' => [ self::HAS_MANY, LeadLabel::class, 'id_lead', 'id' ],
  ];

  public function columns(array $columns = []): array
  {
    return parent::columns(array_merge($columns, [

      'title' => [
        'type' => 'varchar',
        'title' => 'Title',
        'required' => true,
      ],
      'id_company' => [
        'type' => 'lookup',
        'title' => 'Company',
        'model' => 'CeremonyCrmApp/Modules/Core/Customers/Models/Company',
        'foreignKeyOnUpdate' => 'CASCADE',
        'foreignKeyOnDelete' => 'RESTRICT',
        'required' => true,
      ],
      'id_person' => [
        'type' => 'lookup',
        'title' => 'Contact Person',
        'model' => 'CeremonyCrmApp/Modules/Core/Customers/Models/Person',
        'foreignKeyOnUpdate' => 'CASCADE',
        'foreignKeyOnDelete' => 'SET NULL',
        'required' => true,
      ],
      'price' => [
        'type' => 'float',
        'title' => 'Amount',
        'required' => true,
      ],
      'id_currency' => [
        'type' => 'lookup',
        'title' => 'Currency',
        'model' => 'CeremonyCrmApp/Modules/Core/Settings/Models/Currency',
        'foreignKeyOnUpdate' => 'CASCADE',
        'foreignKeyOnDelete' => 'SET NULL',
        'required' => true,
      ],
      'date_expected_close' => [
        'type' => 'date',
        'title' => 'Expected Close Date',
        'required' => false,
      ],
      'id_user' => [
        'type' => 'lookup',
        'title' => 'Owner',
        'model' => 'CeremonyCrmApp/Modules/Core/Settings/Models/User',
        'foreignKeyOnUpdate' => 'RESTRICT',
        'foreignKeyOnDelete' => 'RESTRICT',
        'required' => true,
      ],
      'id_status' => [
        'type' => 'lookup',
        'title' => 'Status',
        'model' => 'CeremonyCrmApp/Modules/Core/Settings/Models/LeadStatus',
        'foreignKeyOnUpdate' => 'CASCADE',
        'foreignKeyOnDelete' => 'SET NULL',
        'required' => true,
      ],
      'note' => [
        'type' => 'text',
        'title' => 'Notes',
        'required' => false,
      ],
      'source_channel' => [
        'type' => 'varchar',
        'title' => 'Source Channel',
        'required' => false,
      ],
      'is_archived' => [
        'type' => 'boolean',
        'title' => 'Archived',
        'required' => false,
      ],
    ]));
  }

  public function tableDescribe(array $description = []): array
  {
    $description = parent::tableDescribe();
    $description['ui']['title'] = 'Leads';
    $description['ui']['addButtonText'] = 'Add Lead';
    $description['ui']['showHeader'] = true;
    $description['ui']['showFooter'] = false;
    $description['columns']['labels'] = ["title" => "Labels"];
    unset($description['columns']['note']);
    unset($description['columns']['id_person']);
    unset($description['columns']['source_channel']);
    unset($description['columns']['is_archived']);
    return $description;
  }

  public function formDescribe(array $description = []): array
  {
    $description = parent::formDescribe();
    $description['defaultValues']['is_archived'] = 0;
    $description['defaultValues']['id_status'] = 1;
    $description['includeRelations'] = ['DEAL','COMPANY', 'USER', 'PERSON', 'STATUS', 'CURRENCY', 'HISTORY', 'LABELS'];
    return $description;
  }

  public function onBeforeUpdate(array $record): array
  {
    $lead = $this->eloquent->find($record["id"])->toArray();
    $mLeadHistory = new LeadHistory($this->app);
    $mLeadStatus= new LeadStatus($this->app);

    if ($lead["id_status"] != (int) $record["id_status"]) {
      $status = $mLeadStatus->eloquent->find((int) $record["id_status"])->name;
      $mLeadHistory->eloquent->create([
        "change_date" => date("Y-m-d"),
        "id_lead" => $record["id"],
        "description" => "Status changed to ".$status
      ]);
    }
    if ((float) $lead["price"] != (float) $record["price"]) {
      $mLeadHistory->eloquent->create([
        "change_date" => date("Y-m-d"),
        "id_lead" => $record["id"],
        "description" => "Price changed to ".$record["price"]." ".$record["CURRENCY"]["code"]
      ]);
    }
    if ((string) $lead["date_expected_close"] != (string) $record["date_expected_close"]) {
      $mLeadHistory->eloquent->create([
        "change_date" => date("Y-m-d"),
        "id_lead" => $record["id"],
        "description" => "Expected Close Date changed to ".date("d.m.Y", strtotime($record["date_expected_close"]))
      ]);
    }

    return $record;
  }

  public function onAfterCreate(array $record, $returnValue)
  {
    $mLeadHistory = new LeadHistory($this->app);
    $mLeadHistory->eloquent->create([
      "change_date" => date("Y-m-d"),
      "id_lead" => $record["id"],
      "description" => "Lead created"
    ]);

    return parent::onAfterCreate($record, $returnValue);
  }

  /* public function prepareLoadRecordQuery(?array $includeRelations = null, int $maxRelationLevel = 0, $query = null, int $level = 0)
  {
    $query = parent::prepareLoadRecordQuery();
    $query->orderBy("id_status", "asc");
    return $query;
  } */
}
