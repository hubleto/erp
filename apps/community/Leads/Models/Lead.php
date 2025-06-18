<?php

namespace HubletoApp\Community\Leads\Models;

use ADIOS\Core\Db\Column\Boolean;
use ADIOS\Core\Db\Column\Integer;
use ADIOS\Core\Db\Column\Date;
use ADIOS\Core\Db\Column\DateTime;
use ADIOS\Core\Db\Column\Decimal;
use ADIOS\Core\Db\Column\Lookup;
use ADIOS\Core\Db\Column\Text;
use ADIOS\Core\Db\Column\Varchar;
use HubletoApp\Community\Contacts\Models\Contact;
use HubletoApp\Community\Customers\Models\Customer;
use HubletoApp\Community\Deals\Models\Deal;
use HubletoApp\Community\Products\Controllers\Api\CalculatePrice;
use HubletoApp\Community\Settings\Models\Currency;
use HubletoApp\Community\Settings\Models\Setting;
use HubletoApp\Community\Settings\Models\User;
use HubletoMain\Core\Helper;

class Lead extends \HubletoMain\Core\Models\Model
{
  public string $table = 'leads';
  public string $recordManagerClass = RecordManagers\Lead::class;
  public ?string $lookupSqlValue = 'concat(ifnull({%TABLE%}.identifier, ""), " ", ifnull({%TABLE%}.title, ""))';
  public ?string $lookupUrlDetail = 'leads/{%ID%}';

  const STATUS_NEW = 1;
  const STATUS_IN_PROGRESS = 2;
  const STATUS_COMPLETED = 3;
  const STATUS_LOST = 4;

  public array $relations = [
    'DEAL' => [ self::HAS_ONE, Deal::class, 'id_lead', 'id'],
    'CAMPAIGN' => [ self::BELONGS_TO, Campaign::class, 'id_campaign', 'id' ],
    'CUSTOMER' => [ self::BELONGS_TO, Customer::class, 'id_customer', 'id' ],
    'OWNER' => [ self::BELONGS_TO, User::class, 'id_owner', 'id'],
    'MANAGER' => [ self::BELONGS_TO, User::class, 'id_manager', 'id'],
    'CONTACT' => [ self::HAS_ONE, Contact::class, 'id', 'id_contact'],
    'CURRENCY' => [ self::HAS_ONE, Currency::class, 'id', 'id_currency'],
    'HISTORY' => [ self::HAS_MANY, LeadHistory::class, 'id_lead', 'id', ],
    'TAGS' => [ self::HAS_MANY, LeadTag::class, 'id_lead', 'id' ],
    'PRODUCTS' => [ self::HAS_MANY, LeadProduct::class, 'id_lead', 'id' ],
    'SERVICES' => [ self::HAS_MANY, LeadProduct::class, 'id_lead', 'id' ],
    'ACTIVITIES' => [ self::HAS_MANY, LeadActivity::class, 'id_lead', 'id' ],
    'DOCUMENTS' => [ self::HAS_MANY, LeadDocument::class, 'id_lookup', 'id'],
  ];

  public function describeColumns(): array
  {
    return array_merge(parent::describeColumns(), [
      'identifier' => (new Varchar($this, $this->translate('Lead Identifier'))),
      'title' => (new Varchar($this, $this->translate('Title')))->setRequired(),
      'id_campaign' => (new Lookup($this, $this->translate('Campaign'), Campaign::class))->setFkOnUpdate('CASCADE')->setFkOnDelete('RESTRICT'),
      'id_customer' => (new Lookup($this, $this->translate('Customer'), Customer::class))->setFkOnUpdate('CASCADE')->setFkOnDelete('RESTRICT'),
      'id_contact' => (new Lookup($this, $this->translate('Contact'), Contact::class))->setFkOnUpdate('CASCADE')->setFkOnDelete('SET NULL')->setRequired(),
      'status' => (new Integer($this, $this->translate('Status')))->setRequired()->setEnumValues(
        [ $this::STATUS_NEW => 'New', $this::STATUS_IN_PROGRESS => 'In Progress', $this::STATUS_COMPLETED => 'Completed', $this::STATUS_LOST => 'Lost' ]
      )->setEnumCssClasses([
        self::STATUS_NEW => 'bg-blue-100 text-blue-800',
        self::STATUS_IN_PROGRESS => 'bg-yellow-100 text-yellow-800',
        self::STATUS_COMPLETED => 'bg-green-100 text-green-800',
        self::STATUS_LOST => 'bg-red-100 text-red-800',
      ]),
      'price' => (new Decimal($this, $this->translate('Price'))),
      'id_currency' => (new Lookup($this, $this->translate('Currency'), Currency::class))->setFkOnUpdate('CASCADE')->setFkOnDelete('SET NULL')->setReadonly(),
      'score' => (new Integer($this, $this->translate('Score')))->setColorScale('bg-light-blue-to-dark-blue'),
      'date_expected_close' => (new Date($this, $this->translate('Expected close date'))),
      'id_owner' => (new Lookup($this, $this->translate('Owner'), User::class)),
      'id_manager' => (new Lookup($this, $this->translate('Manager'), User::class)),
      'date_created' => (new DateTime($this, $this->translate('Created')))->setRequired()->setReadonly(),
      'lost_reason' => (new Lookup($this, $this->translate("Reason for Lost"), LostReason::class)),
      'shared_folder' => new Varchar($this, "Online document folder"),
      'note' => (new Text($this, $this->translate('Notes'))),
      'source_channel' => (new Integer($this, $this->translate('Source channel')))->setEnumValues([
        1 => "Advertisement",
        2 => "Partner",
        3 => "Web",
        4 => "Cold call",
        5 => "E-mail",
        6 => "Refferal",
        7 => "Other",
      ]),
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
          // ->setDescription($this->translate('Link to shared folder (online storage) with related documents'))
        ;
      break;
      case 'status':
        $description->setEnumCssClasses([
          self::STATUS_NEW => 'bg-yellow-50',
          self::STATUS_IN_PROGRESS => 'bg-yellow-50',
          self::STATUS_COMPLETED => 'bg-yellow-50',
          self::STATUS_LOST => 'bg-yellow-50',
        ]);
      break;
      // case 'id_customer':
      //   $description ->setExtendedProps(['urlAdd' => 'customers/add']);
      // break;
    }
    return $description;
  }

  public function describeTable(): \ADIOS\Core\Description\Table
  {
    $description = parent::describeTable();
    $description->ui['showHeader'] = true;
    $description->ui['showFooter'] = false;
    $description->ui['showFulltextSearch'] = true;
    $description->ui['showColumnSearch'] = true;
    $description->columns['tags'] = ["title" => "Tags"];

    unset($description->columns['note']);
    unset($description->columns['price']);
    unset($description->columns['id_currency']);
    unset($description->columns['date_created']);
    unset($description->columns['id_customer']);
    unset($description->columns['source_channel']);
    unset($description->columns['is_archived']);
    unset($description->columns['shared_folder']);
    // unset($description->columns['lost_reason']);

    // if ($this->main->urlParamAsInteger('idCustomer') > 0) {
    //   $description->permissions = [
    //     'canRead' => $this->main->permissions->granted($this->fullName . ':Read'),
    //     'canCreate' => $this->main->permissions->granted($this->fullName . ':Create'),
    //     'canUpdate' => $this->main->permissions->granted($this->fullName . ':Update'),
    //     'canDelete' => $this->main->permissions->granted($this->fullName . ':Delete'),
    //   ];
    //   // $description->columns = [];
    //   // $description->inputs = [];
    //   // $description->ui = [];
    // }
    if ($this->main->urlParamAsBool("showArchive")) {
      $description->permissions = [
        "canCreate" => false,
        "canUpdate" => false,
        "canRead" => true,
        "canDelete" => $this->main->permissions->granted($this->fullName . ':Delete')
      ];
    } else {
      $description->ui['addButtonText'] = $this->translate('Add lead');
    }

    return $description;
  }

  public function describeForm(): \ADIOS\Core\Description\Form
  {
    $description = parent::describeForm();

    $mSettings = new Setting($this->main);
    $defaultCurrency = (int) $mSettings->record
      ->where("key", "Apps\Community\Settings\Currency\DefaultCurrency")
      ->first()
      ->value
    ;

    $description->defaultValues['id_customer'] = $this->main->urlParamAsInteger('idCustomer');
    $description->defaultValues['date_created'] = date("Y-m-d H:i:s");
    $description->defaultValues['id_contact'] = null;
    $description->defaultValues['price'] = 0;
    $description->defaultValues['is_archived'] = 0;
    $description->defaultValues['id_currency'] = $defaultCurrency;
    $description->defaultValues['status'] = $this::STATUS_NEW;
    $description->defaultValues['id_owner'] = $this->main->auth->getUserId();
    $description->defaultValues['id_manager'] = $this->main->auth->getUserId();

    $description->ui['addButtonText'] = $this->translate('Add Lead');

    return $description;
  }

  public function checkOwnership(array $record): void
  {
    if (isset($record['id_customer']) && $record['id_customer'] && !isset($record['checkOwnership'])) {
      $mCustomer = new Customer($this->main);
      $customer = $mCustomer->record
        ->where("id", (int) $record["id_customer"])
        ->first()
      ;

      // Dusan 30.5.2025: Toto robilo problemy, ked som pri testovani rucne zmenil ownera zaznamu.
      // if ($customer->id_owner != (int) $record["id_owner"]) {
      //   throw new \Exception("This lead cannot be assigned to the selected user,\nbecause they are not assigned to the selected customer.");
      // }
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

    $lead = $this->record->find($record["id"])->toArray();
    $mLeadHistory = new LeadHistory($this->main);

    if (isset($record['price']) && (float) $lead["price"] != (float) $record["price"]) {
      $mLeadHistory->record->recordCreate([
        "change_date" => date("Y-m-d"),
        "id_lead" => $record["id"],
        "description" => "Price changed to " . (string) $record["price"],
      ]);
    }
    if (isset($record['date_expected_close']) && (string) $lead["date_expected_close"] != (string) $record["date_expected_close"]) {
      $mLeadHistory->record->recordCreate([
        "change_date" => date("Y-m-d"),
        "id_lead" => $record["id"],
        "description" => "Expected Close Date changed to " . date("d.m.Y", (int) strtotime((string) $record["date_expected_close"])),
      ]);
    }

    return $record;
  }

  public function onAfterCreate(array $originalRecord, array $savedRecord): array
  {
    $savedRecord = parent::onAfterCreate($originalRecord, $savedRecord);

    $mLeadHistory = new LeadHistory($this->main);
    $mLeadHistory->record->recordCreate([
      "change_date" => date("Y-m-d"),
      "id_lead" => $savedRecord["id"],
      "description" => "Lead created"
    ]);

    $newLead = $savedRecord;
    if (empty($newLead['identifier'])) {
      $newLead["identifier"] = $this->main->apps->community('Leads')->configAsString('leadPrefix') . str_pad($savedRecord["id"], 6, 0, STR_PAD_LEFT);
      $this->record->recordUpdate($newLead);
    }

    return $savedRecord;
  }

  public function onAfterUpdate(array $originalRecord, array $savedRecord): array
  {
    $savedRecord = parent::onAfterUpdate($originalRecord, $savedRecord);

    if (isset($savedRecord["TAGS"])) {
      $helper = new Helper($this->main, $this->app);
      $helper->deleteTags(
        array_column($savedRecord["TAGS"], "id"),
        "HubletoApp/Community/Leads/Models/LeadTag",
        "id_lead",
        $savedRecord["id"]
      );
    }

    // $sums = 0;
    // $calculator = new CalculatePrice($this->main);
    // $allProducts = array_merge($savedRecord["PRODUCTS"], $savedRecord["SERVICES"]);

    // if (!empty($allProducts)) {
    //   foreach ($allProducts as $product) {
    //     if (!isset($product["_toBeDeleted_"])) {
    //       $sums += $calculator->calculatePriceIncludingVat(
    //         $product["unit_price"],
    //         $product["amount"],
    //         $product["vat"] ?? 0,
    //         $product["discount"] ?? 0
    //       );
    //     }
    //   }
    //   $this->record->find($savedRecord["id"])->update(["price" => $sums]);
    // }

    return $savedRecord;
  }
}
