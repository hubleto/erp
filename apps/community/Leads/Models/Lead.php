<?php

namespace HubletoApp\Community\Leads\Models;

use HubletoApp\Community\Customers\Models\Company;
use HubletoApp\Community\Customers\Models\Person;
use HubletoApp\Community\Settings\Models\Currency;
use HubletoApp\Community\Settings\Models\User;
use HubletoApp\Community\Deals\Models\Deal;
use HubletoApp\Community\Leads\Models\LeadHistory;
use HubletoApp\Community\Leads\Models\LeadTag;

use \ADIOS\Core\Db\Column\Lookup;
use \ADIOS\Core\Db\Column\Varchar;
use \ADIOS\Core\Db\Column\Date;
use \ADIOS\Core\Db\Column\Text;
use \ADIOS\Core\Db\Column\Decimal;
use \ADIOS\Core\Db\Column\Boolean;

class Lead extends \HubletoMain\Core\Model
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
    'TAGS' => [ self::HAS_MANY, LeadTag::class, 'id_lead', 'id' ],
    'SERVICES' => [ self::HAS_MANY, LeadService::class, 'id_lead', 'id' ],
    'ACTIVITIES' => [ self::HAS_MANY, LeadActivity::class, 'id_lead', 'id' ],
    'DOCUMENTS' => [ self::HAS_MANY, LeadDocument::class, 'id_lead', 'id'],
  ];

  public function columns(array $columns = []): array
  {
    return parent::columns(array_merge($columns, [
      'title' => (new Varchar($this, $this->translate('Title')))->setRequired(),
      'id_company' => (new Lookup($this, $this->translate('Company'), Company::class))->setFkOnUpdate('CASCADE')->setFkOnDelete('RESTRICT'),
      'id_person' => (new Lookup($this, $this->translate('Contact person'), Person::class))->setFkOnUpdate('CASCADE')->setFkOnDelete('SET NULL'),
      'price' => (new Decimal($this, $this->translate('Price'))),
      'id_currency' => (new Lookup($this, $this->translate('Currency'), Currency::class))->setFkOnUpdate('CASCADE')->setFkOnDelete('SET NULL'),
      'date_expected_close' => (new Date($this, $this->translate('Expected close date'))),
      'id_user' => (new Lookup($this, $this->translate('Assigned user'), User::class))->setRequired(),
      'date_created' => (new Date($this, $this->translate('Date created')))->setRequired()->setReadonly(),
      'id_lead_status' => (new Lookup($this, $this->translate('Status'), LeadStatus::class))->setRequired(),
      'note' => (new Text($this, $this->translate('Notes'))),
      'source_channel' => (new Varchar($this, $this->translate('Source channel'))),
      'is_archived' => (new Boolean($this, $this->translate('Archived'))),
    ]));
  }

  public function describeTable(): \ADIOS\Core\Description\Table
  {
    $description["model"] = $this->fullName;
    $description = parent::describeTable();
    if ($this->main->urlParamAsBool("showArchive")) {
      $description->ui['title'] = "Leads Archive";
      $description->permissions = [
        "canCreate" => false,
        "canUpdate" => false,
        "canRead" => true,
        "canDelete" => $this->main->permissions->granted($this->fullName . ':Delete')
      ];
    } else {
      $description->ui['title'] = 'Leads';
      $description->ui['addButtonText'] = 'Add Lead';
    }
    $description->ui['showHeader'] = true;
    $description->ui['showFooter'] = false;
    $description->columns['tags'] = ["title" => "Tags"];
    unset($description->columns['note']);
    unset($description->columns['id_person']);
    unset($description->columns['source_channel']);
    unset($description->columns['is_archived']);

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
    $description = parent::describeForm();

    $description->defaultValues['id_company'] = null;
    $description->defaultValues['date_created'] = date("Y-m-d");
    $description->defaultValues['id_person'] = null;
    $description->defaultValues['is_archived'] = 0;
    $description->defaultValues['id_lead_status'] = 1;
    $description->defaultValues['id_user'] = $this->main->auth->getUserId();

    $description->ui['addButtonText'] = $this->translate('Add lead');

    return $description;
  }

  public function prepareLoadRecordQuery(array $includeRelations = [], int $maxRelationLevel = 0, mixed $query = null, int $level = 0): mixed
  {
    $relations = [
      'DEAL',
      'COMPANY',
      'USER',
      'PERSON',
      'STATUS',
      'CURRENCY',
      'HISTORY',
      'TAGS',
      'SERVICES',
      'ACTIVITIES',
      'DOCUMENTS',
    ];
    $query = parent::prepareLoadRecordQuery($relations, 4);

    if ($this->main->urlParamAsBool("showArchive")) {
      $query = $query->where("leads.is_archived", 1);
    } else {
      $query = $query->where("leads.is_archived", 0);
    }

    return $query;
  }

  public function checkOwnership(array $record): void
  {
    if ($record["id_company"] && !isset($record["checkOwnership"])) {
      $mCompany = new Company($this->main);
      $company = $mCompany->eloquent
        ->where("id", (int) $record["id_company"])
        ->first()
      ;

      if ($company->id_user != (int) $record["id_user"]) {
        throw new \Exception("This lead cannot be assigned to the selected user,\nbecause they are not assigned to the selected company.");
      }
    }
  }

  public function onBeforeCreate(array $record): array
  {
    $this->checkOwnership($record);
    return $record;
  }

  public function onBeforeUpdate(array $record): array
  {
    $this->checkOwnership($record);

    $lead = $this->eloquent->find($record["id"])->toArray();
    $mLeadHistory = new LeadHistory($this->main);
    $mLeadStatus = new LeadStatus($this->main);

    if ($lead["id_lead_status"] != (int) $record["id_lead_status"]) {
      $status = (string) $mLeadStatus->eloquent->find((int) $record["id_lead_status"])->name;
      $mLeadHistory->eloquent->create([
        "change_date" => date("Y-m-d"),
        "id_lead" => $record["id"],
        "description" => "Status changed to " . $status
      ]);
    }
    if ((float) $lead["price"] != (float) $record["price"]) {
      $mLeadHistory->eloquent->create([
        "change_date" => date("Y-m-d"),
        "id_lead" => $record["id"],
        "description" => "Price changed to " . (string) $record["price"] . " " . (string) $record["CURRENCY"]["code"],
      ]);
    }
    if ((string) $lead["date_expected_close"] != (string) $record["date_expected_close"]) {
      $mLeadHistory->eloquent->create([
        "change_date" => date("Y-m-d"),
        "id_lead" => $record["id"],
        "description" => "Expected Close Date changed to " . date("d.m.Y", (int) strtotime((string) $record["date_expected_close"])),
      ]);
    }

    return $record;
  }

  public function onAfterCreate(array $originalRecord, array $savedRecord): array
  {
    $mLeadHistory = new LeadHistory($this->main);
    $mLeadHistory->eloquent->create([
      "change_date" => date("Y-m-d"),
      "id_lead" => $savedRecord["id"],
      "description" => "Lead created"
    ]);

    return parent::onAfterCreate($originalRecord, $savedRecord);
  }
}
