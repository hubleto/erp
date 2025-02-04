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

use \ADIOS\Core\Db\Column\Lookup;
use \ADIOS\Core\Db\Column\Varchar;
use \ADIOS\Core\Db\Column\Date;
use \ADIOS\Core\Db\Column\Text;
use \ADIOS\Core\Db\Column\Decimal;
use \ADIOS\Core\Db\Column\Boolean;

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
      'title' => (new Varchar($this, $this->translate('Title')))->setRequired(),
      'id_company' => (new Lookup($this, $this->translate('Company'), Company::class))->setFkOnUpdate('CASCADE')->setFkOnDelete('RESTRICT')->setRequired(),
      'id_person' => (new Lookup($this, $this->translate('Contact person'), Person::class))->setFkOnUpdate('CASCADE')->setFkOnDelete('SET NULL'),
      'id_lead' => (new Lookup($this, $this->translate('Lead'), Lead::class))->setFkOnUpdate('CASCADE')->setFkOnDelete('SET NULL')->setReadonly(),
      'price' => (new Decimal($this, $this->translate('Price')))->setRequired(),
      'id_currency' => (new Lookup($this, $this->translate('Currency'), Currency::class))->setFkOnUpdate('RESTRICT')->setFkOnDelete('SET NULL')->setRequired(),
      'date_expected_close' => (new Date($this, $this->translate('Expected close date'))),
      'id_user' => (new Lookup($this, $this->translate('Assigned user'), User::class))->setRequired(),
      'date_created' => (new Date($this, $this->translate('Date created')))->setRequired()->setReadonly(),
      'id_pipeline' => (new Lookup($this, $this->translate('Pipeline'), Pipeline::class))->setFkOnUpdate('CASCADE')->setFkOnDelete('SET NULL'),
      'id_pipeline_step' => (new Lookup($this, $this->translate('Pipeline step'), PipelineStep::class))->setFkOnUpdate('CASCADE')->setFkOnDelete('SET NULL'),
      'note' => (new Text($this, $this->translate('Notes'))),
      'source_channel' => (new Varchar($this, $this->translate('Source channel'))),
      'is_archived' => (new Boolean($this, $this->translate('Archived'))),
      'id_deal_status' => (new Lookup($this, $this->translate('Status'), DealStatus::class))->setFkOnUpdate('CASCADE')->setFkOnDelete('SET NULL'),
    ]));
  }

  public function describeTable(): \ADIOS\Core\Description\Table
  {
    $description = parent::describeTable();
    if ($this->main->urlParamAsBool("showArchive")) {
      $description->ui['title'] = "Deals Archive";
      $description->permissions = [
        "canCreate" => false,
        "canUpdate" => false,
        "canRead" => true,
        "canDelete" => $this->main->permissions->granted($this->fullName . ':Delete')
      ];
    } else {
      $description->ui['title'] = 'Deal';
      $description->ui['addButtonText'] = 'Add Deal';
    }
    $description->ui['showHeader'] = true;
    $description->ui['showFooter'] = false;
    $description->columns['tags'] = ["title" => "Tags"];
    unset($description->columns['note']);
    unset($description->columns['id_person']);
    unset($description->columns['source_channel']);
    unset($description->columns['is_archived']);
    unset($description->columns['id_lead']);
    unset($description->columns['id_pipeline']);

    if ($this->main->urlParamAsInteger('idCompany') > 0) {
      $description->permissions = [
        'canRead' => $this->main->permissions->granted($this->fullName . ':Read'),
        'canCreate' => $this->main->permissions->granted($this->fullName . ':Create'),
        'canUpdate' => $this->main->permissions->granted($this->fullName . ':Update'),
        'canDelete' => $this->main->permissions->granted($this->fullName . ':Delete'),
      ];
      $description->columns = [];
      $description->ui = [];
    }

    return $description;
  }

  public function describeForm(): \ADIOS\Core\Description\Form
  {
    $mSettings = new Setting($this->main);
    $defaultPipeline =(int) $mSettings->eloquent
      ->where("key", "Modules\Core\Settings\Pipeline\DefaultPipeline")
      ->first()
      ->value
    ;

    $description = parent::describeForm();
    $description->defaultValues['is_archived'] = 0;
    $description->defaultValues['id_deal_status'] = 1;
    $description->defaultValues['date_created'] = date("Y-m-d");
    $description->defaultValues['id_pipeline'] = $defaultPipeline;
    $description->defaultValues['id_pipeline_step'] = null;
    $description->defaultValues['id_user'] = $this->main->auth->getUserId();

    return $description;
  }

  public function prepareLoadRecordQuery(array $includeRelations = [], int $maxRelationLevel = 0, mixed $query = null, int $level = 0): mixed
  {
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
    if ($this->main->urlParamAsBool("showArchive")) {
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
      "id_deal" => $savedRecord["id"],
      "description" => "Deal created"
    ]);

    return parent::onAfterCreate($originalRecord, $savedRecord);
  }

  public function getOwnership(array $record): void
  {
    if ($record["id_company"] && !isset($record["checkOwnership"])) {
      $mCompany = new Company($this->main);
      $company = $mCompany->eloquent
        ->where("id", $record["id_company"])
        ->first()
      ;

      if ($company->id_user != $record["id_user"]) {
        throw new \Exception("This deal cannot be assigned to the selected user,\nbecause they are not assigned to the selected company.");
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
        "id_deal" => (int) ($record["id"] ?? 0),
        "description" => "Price changed to " . (string) ($record["price"] ?? '') . " " . (string) ($record["CURRENCY"]["code"] ?? '')
      ]);
    }
    if ((string) $deal["date_expected_close"] != (string) $record["date_expected_close"]) {
      $mDealHistory->eloquent->create([
        "change_date" => date("Y-m-d"),
        "id_deal" => $record["id"],
        "description" => "Expected Close Date changed to ".date("d.m.Y", (int) strtotime((string) $record["date_expected_close"]))
      ]);
    }

    return $record;
  }
}
