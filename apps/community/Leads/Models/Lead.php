<?php

namespace HubletoApp\Community\Leads\Models;

use HubletoApp\Community\Customers\Models\Customer;
use HubletoApp\Community\Contacts\Models\Person;
use HubletoApp\Community\Settings\Models\Currency;
use HubletoApp\Community\Settings\Models\User;
use HubletoApp\Community\Deals\Models\Deal;
use HubletoApp\Community\Leads\Models\LeadHistory;
use HubletoApp\Community\Leads\Models\LeadTag;
use HubletoApp\Community\Settings\Models\Setting;
use HubletoApp\Community\Leads\Models\LeadProduct;

use \ADIOS\Core\Db\Column\Lookup;
use \ADIOS\Core\Db\Column\Varchar;
use \ADIOS\Core\Db\Column\Date;
use \ADIOS\Core\Db\Column\Text;
use \ADIOS\Core\Db\Column\Decimal;
use \ADIOS\Core\Db\Column\Boolean;
use \ADIOS\Core\Db\Column\DateTime;
use HubletoMain\Core\Helper;
use Illuminate\Database\Eloquent\Builder;

class Lead extends \HubletoMain\Core\Model
{
  public string $table = 'leads';
  public string $eloquentClass = Eloquent\Lead::class;
  public ?string $lookupSqlValue = '{%TABLE%}.title';

  public array $relations = [
    'DEAL' => [ self::HAS_ONE, Deal::class, 'id_lead', 'id'],
    'CUSTOMER' => [ self::BELONGS_TO, Customer::class, 'id_customer', 'id' ],
    'USER' => [ self::BELONGS_TO, User::class, 'id_user', 'id'],
    'PERSON' => [ self::HAS_ONE, Person::class, 'id', 'id_person'],
    'STATUS' => [ self::HAS_ONE, LeadStatus::class, 'id', 'id_lead_status'],
    'CURRENCY' => [ self::HAS_ONE, Currency::class, 'id', 'id_currency'],
    'HISTORY' => [ self::HAS_MANY, LeadHistory::class, 'id_lead', 'id', ],
    'TAGS' => [ self::HAS_MANY, LeadTag::class, 'id_lead', 'id' ],
    'PRODUCTS' => [ self::HAS_MANY, LeadProduct::class, 'id_lead', 'id' ],
    'ACTIVITIES' => [ self::HAS_MANY, LeadActivity::class, 'id_lead', 'id' ],
    'DOCUMENTS' => [ self::HAS_MANY, LeadDocument::class, 'id_lookup', 'id'],
  ];

  public function describeColumns(): array
  {
    return array_merge(parent::describeColumns(), [
      'identifier' => (new Varchar($this, $this->translate('Lead Identifier'))),
      'title' => (new Varchar($this, $this->translate('Title')))->setRequired(),
      'id_customer' => (new Lookup($this, $this->translate('Customer'), Customer::class))->setFkOnUpdate('CASCADE')->setFkOnDelete('RESTRICT'),
      'id_person' => (new Lookup($this, $this->translate('Contact person'), Person::class))->setFkOnUpdate('CASCADE')->setFkOnDelete('SET NULL'),
      'price' => (new Decimal($this, $this->translate('Price'))),
      'id_currency' => (new Lookup($this, $this->translate('Currency'), Currency::class))->setFkOnUpdate('CASCADE')->setFkOnDelete('SET NULL')->setReadonly(),
      'date_expected_close' => (new Date($this, $this->translate('Expected close date'))),
      'id_user' => (new Lookup($this, $this->translate('Assigned user'), User::class))->setRequired(),
      'date_created' => (new DateTime($this, $this->translate('Date created')))->setRequired()->setReadonly(),
      'id_lead_status' => (new Lookup($this, $this->translate('Status'), LeadStatus::class))->setRequired(),
      'shared_folder' => new Varchar($this, "Shared folder (online document storage)"),
      'note' => (new Text($this, $this->translate('Notes'))),
      'source_channel' => (new Varchar($this, $this->translate('Source channel'))),
      'is_archived' => (new Boolean($this, $this->translate('Archived'))),
    ]);
  }

  public function describeInput(string $columnName): \ADIOS\Core\Description\Input
  {
    $description = parent::describeInput($columnName);
    switch ($columnName) {
      case 'shared_folder':
        $description
          ->setReactComponent('InputHyperlink')
          ->setDescription($this->translate('Link to shared folder (online storage) with related documents'))
        ;
      break;
    }
    return $description;
  }

  public function describeTable(): \ADIOS\Core\Description\Table
  {
    $description = parent::describeTable();
    $description->ui['showHeader'] = true;
    $description->ui['showFulltextSearch'] = true;
    $description->ui['showFooter'] = false;
    $description->ui['showFulltextSearch'] = true;
    $description->columns['tags'] = ["title" => "Tags"];

    unset($description->columns['note']);
    unset($description->columns['id_person']);
    unset($description->columns['source_channel']);
    unset($description->columns['is_archived']);
    unset($description->columns['shared_folder']);

    if ($this->main->urlParamAsInteger('idCustomer') > 0) {
      $description->permissions = [
        'canRead' => $this->main->permissions->granted($this->fullName . ':Read'),
        'canCreate' => $this->main->permissions->granted($this->fullName . ':Create'),
        'canUpdate' => $this->main->permissions->granted($this->fullName . ':Update'),
        'canDelete' => $this->main->permissions->granted($this->fullName . ':Delete'),
      ];
      $description->columns = [];
      $description->inputs = [];
      $description->ui = [];
    }
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

    return $description;
  }

  public function describeForm(): \ADIOS\Core\Description\Form
  {
    $description = parent::describeForm();

    $mSettings = new Setting($this->main);
    $defaultCurrency = (int) $mSettings->eloquent
      ->where("key", "Apps\Community\Settings\Currency\DefaultCurrency")
      ->first()
      ->value
    ;

    $description->defaultValues['id_customer'] = null;
    $description->defaultValues['date_created'] = date("Y-m-d H:i:s");
    $description->defaultValues['id_person'] = null;
    $description->defaultValues['is_archived'] = 0;
    $description->defaultValues['id_currency'] = $defaultCurrency;
    $description->defaultValues['id_lead_status'] = 1;
    $description->defaultValues['id_user'] = $this->main->auth->getUserId();

    $description->ui['addButtonText'] = $this->translate('Add Lead');

    return $description;
  }

  public function checkOwnership(array $record): void
  {
    if ($record["id_customer"] && !isset($record["checkOwnership"])) {
      $mCustomer = new Customer($this->main);
      $customer = $mCustomer->eloquent
        ->where("id", (int) $record["id_customer"])
        ->first()
      ;

      if ($customer->id_user != (int) $record["id_user"]) {
        throw new \Exception("This lead cannot be assigned to the selected user,\nbecause they are not assigned to the selected customer.");
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
        "description" => "Price changed to " . (string) $record["price"],
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

  public function onAfterUpdate(array $originalRecord, array $savedRecord): array
  {
    if (isset($originalRecord["TAGS"])) {
      $helper = new Helper($this->main, $this->app);
      $helper->deleteTags(
        array_column($originalRecord["TAGS"], "id"),
        "HubletoApp/Community/Leads/Models/LeadTag",
        "id_lead",
        $originalRecord["id"]
      );
    }
    return $savedRecord;
  }
}
