<?php

namespace CeremonyCrmApp\Modules\Sales\Leads\Models;

use CeremonyCrmApp\Modules\Core\Customers\Models\Company;
use CeremonyCrmApp\Modules\Core\Customers\Models\Person;
use CeremonyCrmApp\Modules\Core\Settings\Models\Currency;
use CeremonyCrmApp\Modules\Core\Settings\Models\LeadStatus;
use CeremonyCrmApp\Modules\Core\Settings\Models\User;
use CeremonyCrmApp\Modules\Sales\Deals\Models\Deal;
use CeremonyCrmApp\Modules\Sales\Leads\Models\LeadHistory;
use CeremonyCrmApp\Modules\Sales\Leads\Models\LeadLabel;
use Exception;

class Lead extends \CeremonyCrmApp\Core\Model
{
  public string $table = 'leads';
  public string $eloquentClass = Eloquent\Lead::class;
  public ?string $lookupSqlValue = '{%TABLE%}.title';

  public array $relations = [
    'DEAL' => [ self::HAS_ONE, Deal::class, 'id_lead', 'id'],
    'COMPANY' => [ self::BELONGS_TO, Company::class, 'id_company', 'id' ],
    'USER' => [ self::BELONGS_TO, User::class, 'id_user', 'id'],
    'PERSON' => [ self::HAS_ONE, Person::class, 'id', 'id_person'],
    'STATUS' => [ self::HAS_ONE, LeadStatus::class, 'id', 'id_lead_status'],
    'CURRENCY' => [ self::HAS_ONE, Currency::class, 'id', 'id_currency'],
    'HISTORY' => [ self::HAS_MANY, LeadHistory::class, 'id_lead', 'id', ],
    'LABELS' => [ self::HAS_MANY, LeadLabel::class, 'id_lead', 'id' ],
    'SERVICES' => [ self::HAS_MANY, LeadService::class, 'id_lead', 'id' ],
    'ACTIVITIES' => [ self::HAS_MANY, LeadActivity::class, 'id_lead', 'id' ],
    'DOCUMENTS' => [ self::HAS_MANY, LeadDocument::class, 'id_lead', 'id'],
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
        'required' => false,
      ],
      'id_person' => [
        'type' => 'lookup',
        'title' => 'Contact Person',
        'model' => 'CeremonyCrmApp/Modules/Core/Customers/Models/Person',
        'foreignKeyOnUpdate' => 'CASCADE',
        'foreignKeyOnDelete' => 'SET NULL',
        'required' => false,
      ],
      'price' => [
        'type' => 'float',
        'title' => 'Price',
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
        'title' => 'Assigned User',
        'model' => 'CeremonyCrmApp/Modules/Core/Settings/Models/User',
        'foreignKeyOnUpdate' => 'RESTRICT',
        'foreignKeyOnDelete' => 'RESTRICT',
        'required' => true,
      ],
      'date_created' => [
        'type' => 'date',
        'title' => 'Date Created',
        'required' => true,
        'readonly' => true,
      ],
      'id_lead_status' => [
        'type' => 'lookup',
        'title' => 'Status',
        'model' => 'CeremonyCrmApp/Modules/Core/Settings/Models/LeadStatus',
        'foreignKeyOnUpdate' => 'RESTRICT',
        'foreignKeyOnDelete' => 'RESTRICT',
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
    $description["model"] = $this->fullName;
    $description = parent::tableDescribe($description);
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
    $description['defaultValues']['id_company'] = null;
    $description['defaultValues']['date_created'] = date("Y-m-d");
    $description['defaultValues']['id_person'] = null;
    $description['defaultValues']['is_archived'] = 0;
    $description['defaultValues']['id_lead_status'] = 1;
    $description['defaultValues']['id_user'] = $this->app->auth->user["id"];
    $description['includeRelations'] = [
      'DEAL',
      'COMPANY',
      'USER',
      'PERSON',
      'STATUS',
      'CURRENCY',
      'HISTORY',
      'LABELS',
      'SERVICES',
      'ACTIVITIES',
      'DOCUMENTS',
    ];
    return $description;
  }

  public function prepareLoadRecordQuery(?array $includeRelations = null, int $maxRelationLevel = 0, $query = null, int $level = 0)
  {
    $relations = [
      'DEAL',
      'COMPANY',
      'USER',
      'PERSON',
      'STATUS',
      'CURRENCY',
      'HISTORY',
      'LABELS',
      'SERVICES',
      'ACTIVITIES',
      'DOCUMENTS',
    ];
    $query = parent::prepareLoadRecordQuery($relations, 4);

    /**
     * These are the query filters for tables with archived and non-archived lead entries.
     * The params["id"] needs to be there to properly load the data of the entry in a form.
     */
    if (isset($this->app->params["id"])) {
      $query = $query->where("leads.id", (int) $this->app->params["id"]);
    } else if ($this->app->params["archive"] == 1) {
      $query = $query->where("leads.is_archived", 1);
    } else {
      $query = $query->where("leads.is_archived", 0);
    }
    return $query;
  }

  public function getOwnership($record) {
    if ($record["id_company"] && !isset($record["checkOwnership"])) {
      $mCompany = new Company($this->app);
      $company = $mCompany->eloquent
        ->where("id", (int) $record["id_company"])
        ->first()
      ;

      if ($company->id_user != (int) $record["id_user"]) {
        throw new Exception("This lead cannot be assigned to the selected user,\nbecause they are not assigned to the selected company.");
      }
    }
  }

  public function onBeforeCreate(array $record): array
  {
    $this->getOwnership($record);
    return $record;
  }

  public function onBeforeUpdate(array $record): array
  {
    $this->getOwnership($record);

    $lead = $this->eloquent->find($record["id"])->toArray();
    $mLeadHistory = new LeadHistory($this->app);
    $mLeadStatus= new LeadStatus($this->app);

    if ($lead["id_lead_status"] != (int) $record["id_lead_status"]) {
      $status = $mLeadStatus->eloquent->find((int) $record["id_lead_status"])->name;
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
        "description" => "Expected Close Date changed to ".date("d.m.Y", strtotime((string) $record["date_expected_close"]))
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
}
