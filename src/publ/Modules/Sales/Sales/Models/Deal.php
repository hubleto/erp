<?php

namespace CeremonyCrmApp\Modules\Sales\Sales\Models;

use CeremonyCrmApp\Modules\Core\Customers\Models\Company;
use CeremonyCrmApp\Modules\Core\Customers\Models\Person;
use CeremonyCrmApp\Modules\Core\Settings\Models\Currency;
use CeremonyCrmApp\Modules\Core\Settings\Models\DealStatus;
use CeremonyCrmApp\Modules\Core\Settings\Models\User;
use CeremonyCrmApp\Modules\Sales\Sales\Models\DealHistory;
use CeremonyCrmApp\Modules\Sales\Sales\Models\DealLabel;

class Deal extends \CeremonyCrmApp\Core\Model
{
  public string $table = 'deals';
  public string $eloquentClass = Eloquent\Deal::class;
  public ?string $lookupSqlValue = '{%TABLE%}.title';

  public array $relations = [
    'LEAD' => [ self::BELONGS_TO, Lead::class, 'id_deal','id'],
    'COMPANY' => [ self::HAS_ONE, Company::class, 'id', 'id_company'],
    'USER' => [ self::BELONGS_TO, User::class, 'id_user', 'id'],
    'PERSON' => [ self::HAS_ONE, Person::class, 'id', 'id_person'],
    'STATUS' => [ self::HAS_ONE, DealStatus::class, 'id', 'id_status'],
    'CURRENCY' => [ self::HAS_ONE, Currency::class, 'id', 'id_currency'],
    'DEAL_HISTORY' => [ self::HAS_MANY, DealHistory::class, 'id_deal', 'id', ],
    'LABELS' => [ self::HAS_MANY, DealLabel::class, 'id_deal', 'id' ],
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
        'foreignKeyOnUpdate' => 'RESTRICT',
        'foreignKeyOnDelete' => 'RESTRICT',
        'required' => true,
      ],
      'id_person' => [
        'type' => 'lookup',
        'title' => 'Contact Person',
        'model' => 'CeremonyCrmApp/Modules/Core/Customers/Models/Person',
        'foreignKeyOnUpdate' => 'RESTRICT',
        'foreignKeyOnDelete' => 'RESTRICT',
        'required' => true,
      ],
      'id_lead' => [
        'type' => 'lookup',
        'title' => 'Origin Lead',
        'model' => 'CeremonyCrmApp/Modules/Sales/Sales/Models/Lead',
        'foreignKeyOnUpdate' => 'RESTRICT',
        'foreignKeyOnDelete' => 'RESTRICT',
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
        'foreignKeyOnUpdate' => 'RESTRICT',
        'foreignKeyOnDelete' => 'RESTRICT',
        'required' => true,
      ],
      'date_close_expected' => [
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
        'required' => false,
      ],
      'id_status' => [
        'type' => 'lookup',
        'title' => 'Status',
        'model' => 'CeremonyCrmApp/Modules/Core/Settings/Models/DealStatus',
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
    $description = parent::tableDescribe();
    $description['ui']['title'] = 'Deals';
    $description['ui']['addButtonText'] = 'Add Deal';
    $description['ui']['showHeader'] = true;
    $description['ui']['showFooter'] = false;
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
    $description['includeRelations'] = ['COMPANY', 'USER', 'PERSON', 'STATUS', 'CURRENCY', 'DEAL_HISTORY', 'LABELS', 'LEAD'];
    return $description;
  }

  /* public function onBeforeUpdate(array $record): array
  {
    $lead = $this->eloquent->find($record["id"])->toArray();
    $mDealHistory = new DealHistory($this->app);
    $mDealStatus= new DealStatus($this->app);

    if ($lead["id_status"] != (int) $record["id_status"]) {
      $status = $mDealStatus->eloquent->find((int) $record["id_status"])->name;
      $mDealHistory->eloquent->create([
        "change_date" => date("Y-m-d"),
        "id_lead" => $record["id"],
        "description" => "Status changed to ".$status
      ]);
    }

    return $record;
  } */

  /* public function prepareLoadRecordQuery(?array $includeRelations = null, int $maxRelationLevel = 0, $query = null, int $level = 0)
  {
    $query = parent::prepareLoadRecordQuery();
    $query->orderBy("id_status", "asc");
    return $query;
  } */
}
