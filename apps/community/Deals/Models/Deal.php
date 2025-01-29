<?php

namespace HubletoApp\Community\Deals\Models;

use HubletoApp\Community\Customers\Models\Company;
use HubletoApp\Community\Customers\Models\Person;
use HubletoApp\Community\Settings\Models\Currency;
use HubletoApp\Community\Settings\Models\Pipeline;
use HubletoApp\Community\Settings\Models\PipelineStep;
use HubletoApp\Community\Settings\Models\Setting;
use HubletoApp\Community\Settings\Models\User;
use HubletoApp\Community\Deals\Models\DealHistory;
use HubletoApp\Community\Deals\Models\DealTag;
use HubletoApp\Community\Leads\Models\Lead;
use Exception;

class Deal extends \HubletoMain\Core\Model
{
  public string $table = 'deals';
  public string $eloquentClass = Eloquent\Deal::class;
  public ?string $lookupSqlValue = '{%TABLE%}.title';

  public array $relations = [
    'LEAD' => [ self::BELONGS_TO, Lead::class, 'id_lead', 'id'],
    'COMPANY' => [ self::BELONGS_TO, Company::class, 'id_company', 'id' ],
    'USER' => [ self::BELONGS_TO, User::class, 'id_user', 'id'],
    'PERSON' => [ self::HAS_ONE, Person::class, 'id', 'id_person'],
    'PIPELINE' => [ self::HAS_ONE, Pipeline::class, 'id', 'id_pipeline'],
    'STATUS' => [ self::HAS_ONE, DealStatus::class, 'id', 'id_deal_status'],
    'PIPELINE_STEP' => [ self::HAS_ONE, PipelineStep::class, 'id', 'id_pipeline_step'],
    'CURRENCY' => [ self::HAS_ONE, Currency::class, 'id', 'id_currency'],
    'HISTORY' => [ self::HAS_MANY, DealHistory::class, 'id_deal', 'id'],
    'TAGS' => [ self::HAS_MANY, DealTag::class, 'id_deal', 'id' ],
    'SERVICES' => [ self::HAS_MANY, DealService::class, 'id_deal', 'id' ],
    'ACTIVITIES' => [ self::HAS_MANY, DealActivity::class, 'id_deal', 'id' ],
    'DOCUMENTS' => [ self::HAS_MANY, DealDocument::class, 'id_deal', 'id'],
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
        'model' => \HubletoApp\Community\Customers\Models\Company::class,
        'foreignKeyOnUpdate' => 'CASCADE',
        'foreignKeyOnDelete' => 'RESTRICT',
        'required' => true,
      ],
      'id_person' => [
        'type' => 'lookup',
        'title' => 'Contact Person',
        'model' => \HubletoApp\Community\Customers\Models\Person::class,
        'foreignKeyOnUpdate' => 'CASCADE',
        'foreignKeyOnDelete' => 'SET NULL',
        'required' => false,
      ],
      'id_lead' => [
        'type' => 'lookup',
        'title' => 'Origin Lead',
        'model' => \HubletoApp\Community\Leads\Models\Lead::class,
        'foreignKeyOnUpdate' => 'CASCADE',
        'foreignKeyOnDelete' => 'SET NULL',
        'required' => false,
        'readonly' => true,
      ],
      'price' => [
        'type' => 'float',
        'title' => 'Price',
        'required' => true,
      ],
      'id_currency' => [
        'type' => 'lookup',
        'title' => 'Currency',
        'model' => \HubletoApp\Community\Settings\Models\Currency::class,
        'foreignKeyOnUpdate' => 'RESTRICT',
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
        'model' => \HubletoApp\Community\Settings\Models\User::class,
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
      'id_pipeline' => [
        'type' => 'lookup',
        'title' => 'Pipeline',
        'model' => \HubletoApp\Community\Settings\Models\Pipeline::class,
        'foreignKeyOnUpdate' => 'CASCADE',
        'foreignKeyOnDelete' => 'SET NULL',
        'required' => false,
      ],
      'id_pipeline_step' => [
        'type' => 'lookup',
        'title' => 'Pipeline Step',
        'model' => \HubletoApp\Community\Settings\Models\PipelineStep::class,
        'foreignKeyOnUpdate' => 'CASCADE',
        'foreignKeyOnDelete' => 'SET NULL',
        'required' => false,
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
      'id_deal_status' => [
        'type' => 'lookup',
        'title' => 'Status',
        'model' => DealStatus::class,
        'foreignKeyOnUpdate' => 'CASCADE',
        'foreignKeyOnDelete' => 'SET NULL',
        'required' => false,
      ],
    ]));
  }

  public function tableDescribe(array $description = []): array
  {
    $description["model"] = $this->fullName;
    $description = parent::tableDescribe($description);
    if ((bool) $this->main->params["showArchive"]) {
      $description["ui"] = [
        "title" => "Deals Archive"
      ];
      $description["permissions"] = [
        "canCreate" => false,
        "canUpdate" => false,
        "canRead" => true,
        "canDelete" => $this->main->permissions->granted($this->fullName . ':Delete')
      ];
    } else {
      $description['ui'] = [
        'title' => 'Deal',
        'addButtonText' => 'Add Deal'
      ];
    }
    $description['ui']['showHeader'] = true;
    $description['ui']['showFooter'] = false;
    $description['columns']['tags'] = ["title" => "Tags"];
    unset($description['columns']['note']);
    unset($description['columns']['id_person']);
    unset($description['columns']['source_channel']);
    unset($description['columns']['is_archived']);
    unset($description['columns']['id_lead']);
    unset($description['columns']['id_pipeline']);
    return $description;
  }

  public function formDescribe(array $description = []): array
  {
    $mSettings = new Setting($this->main);
    $defaultPipeline =(int) $mSettings->eloquent
      ->where("key", "Modules\Core\Settings\Pipeline\DefaultPipeline")
      ->first()
      ->value
    ;

    $description = parent::formDescribe();
    $description['defaultValues']['is_archived'] = 0;
    $description['defaultValues']['id_deal_status'] = 1;
    $description['defaultValues']['date_created'] = date("Y-m-d");
    $description['defaultValues']['id_pipeline'] = $defaultPipeline;
    $description['defaultValues']['id_pipeline_step'] = null;
    $description['defaultValues']['id_user'] = $this->main->auth->getUserId();
    $description['includeRelations'] = [
      'COMPANY',
      'USER',
      'STATUS',
      'PERSON',
      'PIPELINE',
      'PIPELINE_STEP',
      'CURRENCY',
      'HISTORY',
      'TAGS',
      'LEAD',
      'SERVICES',
      'ACTIVITIES',
      'DOCUMENTS',
    ];
    return $description;
  }

  public function prepareLoadRecordQuery(array|null $includeRelations = null, int $maxRelationLevel = 0, mixed $query = null, int $level = 0): mixed {
    $relations = [
      'COMPANY',
      'USER',
      'STATUS',
      'PERSON',
      'PIPELINE',
      'PIPELINE_STEP',
      'CURRENCY',
      'HISTORY',
      'TAGS',
      'LEAD',
      'SERVICES',
      'ACTIVITIES',
      'DOCUMENTS',
    ];
    $query = parent::prepareLoadRecordQuery($relations, 4);

    /**
     * These are the query filters for tables with archived and non-archived deal entries.
     * The params["id"] needs to be there to properly load the data of the entry in a form.
     */
    if ((bool) ($this->main->params["showArchive"] ?? false)) {
      $query = $query->where("deals.is_archived", 1);
    } else {
      $query = $query->where("deals.is_archived", 0);
    }
    return $query;
  }

  public function onAfterCreate(array $originalRecord, array $savedRecord): array
  {
    $mDealHistory = new DealHistory($this->main);
    $mDealHistory->eloquent->create([
      "change_date" => date("Y-m-d"),
      "id_deal" => $originalRecord["id"],
      "description" => "Deal created"
    ]);

    return $this->main->dispatchEventToPlugins("onModelAfterCreate", [
      "model" => $this,
      "data" => $originalRecord,
      "returnValue" => $savedRecord,
    ])["returnValue"];
  }

  public function getOwnership($record) {
    if ($record["id_company"] && !isset($record["checkOwnership"])) {
      $mCompany = new Company($this->main);
      $company = $mCompany->eloquent
        ->where("id", $record["id_company"])
        ->first()
      ;

      if ($company->id_user != $record["id_user"]) {
        throw new Exception("This deal cannot be assigned to the selected user,\nbecause they are not assigned to the selected company.
        ");
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

    $deal = $this->eloquent->find($record["id"])->toArray();
    $mDealHistory = new DealHistory($this->main);

    if ((float) $deal["price"] != (float) $record["price"]) {
      $mDealHistory->eloquent->create([
        "change_date" => date("Y-m-d"),
        "id_deal" => $record["id"],
        "description" => "Price changed to ".$record["price"]." ".$record["CURRENCY"]["code"]
      ]);
    }
    if ((string) $deal["date_expected_close"] != (string) $record["date_expected_close"]) {
      $mDealHistory->eloquent->create([
        "change_date" => date("Y-m-d"),
        "id_deal" => $record["id"],
        "description" => "Expected Close Date changed to ".date("d.m.Y", strtotime((string) $record["date_expected_close"]))
      ]);
    }

    return $record;
  }
}
