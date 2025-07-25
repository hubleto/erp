<?php

namespace HubletoApp\Community\Deals\Models;

use Hubleto\Framework\Db\Column\Boolean;
use Hubleto\Framework\Db\Column\Date;
use Hubleto\Framework\Db\Column\DateTime;
use Hubleto\Framework\Db\Column\Decimal;
use Hubleto\Framework\Db\Column\Integer;
use Hubleto\Framework\Db\Column\Lookup;
use Hubleto\Framework\Db\Column\Text;
use Hubleto\Framework\Db\Column\Varchar;
use HubletoApp\Community\Contacts\Models\Contact;
use HubletoApp\Community\Customers\Models\Customer;
use HubletoApp\Community\Leads\Models\Lead;
use HubletoApp\Community\Products\Controllers\Api\CalculatePrice;
use HubletoApp\Community\Settings\Models\Currency;
use HubletoApp\Community\Pipeline\Models\Pipeline;
use HubletoApp\Community\Pipeline\Models\PipelineStep;
use HubletoApp\Community\Settings\Models\Setting;
use HubletoApp\Community\Settings\Models\User;
use Hubleto\Framework\Helper;

class Deal extends \Hubleto\Framework\Models\Model
{
  public string $table = 'deals';
  public string $recordManagerClass = RecordManagers\Deal::class;
  public ?string $lookupSqlValue = 'concat(ifnull({%TABLE%}.identifier, ""), " ", ifnull({%TABLE%}.title, ""))';
  public ?string $lookupUrlDetail = 'deals/{%ID%}';

  public const RESULT_UNKNOWN = 1;
  public const RESULT_WON = 2;
  public const RESULT_LOST = 3;

  public const BUSINESS_TYPE_NEW = 1;
  public const BUSINESS_TYPE_EXISTING = 2;

  public const ENUM_SOURCE_CHANNELS = [
    1 => "Advertisement",
    2 => "Partner",
    3 => "Web",
    4 => "Cold call",
    5 => "E-mail",
    6 => "Refferal",
    7 => "Other",
  ];

  public const ENUM_DEAL_RESULTS = [ self::RESULT_UNKNOWN => "Unknown", self::RESULT_WON => "Won", self::RESULT_LOST => "Lost" ];
  public const ENUM_BUSINESS_TYPES = [ self::BUSINESS_TYPE_NEW => "New", self::BUSINESS_TYPE_EXISTING => "Existing" ];

  public array $relations = [
    'LEAD' => [ self::BELONGS_TO, Lead::class, 'id_lead', 'id'],
    'CUSTOMER' => [ self::BELONGS_TO, Customer::class, 'id_customer', 'id' ],
    'OWNER' => [ self::BELONGS_TO, User::class, 'id_owner', 'id'],
    'MANAGER' => [ self::BELONGS_TO, User::class, 'id_manager', 'id'],
    'CONTACT' => [ self::HAS_ONE, Contact::class, 'id', 'id_contact'],
    'PIPELINE' => [ self::HAS_ONE, Pipeline::class, 'id', 'id_pipeline'],
    'PIPELINE_STEP' => [ self::HAS_ONE, PipelineStep::class, 'id', 'id_pipeline_step'],
    'CURRENCY' => [ self::HAS_ONE, Currency::class, 'id', 'id_currency'],
    'HISTORY' => [ self::HAS_MANY, DealHistory::class, 'id_deal', 'id'],
    'TAGS' => [ self::HAS_MANY, DealTag::class, 'id_deal', 'id' ],
    'PRODUCTS' => [ self::HAS_MANY, DealProduct::class, 'id_deal', 'id' ],
    'SERVICES' => [ self::HAS_MANY, DealProduct::class, 'id_deal', 'id' ],
    'ACTIVITIES' => [ self::HAS_MANY, DealActivity::class, 'id_deal', 'id' ],
    'DOCUMENTS' => [ self::HAS_MANY, DealDocument::class, 'id_lookup', 'id'],
  ];

  public function describeColumns(): array
  {
    return array_merge(parent::describeColumns(), [
      'identifier' => (new Varchar($this, $this->translate('Deal Identifier')))->setProperty('defaultVisibility', true),
      'title' => (new Varchar($this, $this->translate('Title')))->setRequired()->setProperty('defaultVisibility', true),
      'id_customer' => (new Lookup($this, $this->translate('Customer'), Customer::class))->setDefaultValue($this->main->urlParamAsInteger('idCustomer')),
      'id_contact' => (new Lookup($this, $this->translate('Contact'), Contact::class)),
      'id_lead' => (new Lookup($this, $this->translate('Lead'), Lead::class))->setReadonly(),
      'price' => (new Decimal($this, $this->translate('Price')))->setDecimals(2),
      'id_currency' => (new Lookup($this, $this->translate('Currency'), Currency::class))->setFkOnUpdate('RESTRICT')->setFkOnDelete('SET NULL')->setReadonly(),
      'date_expected_close' => (new Date($this, $this->translate('Expected close date')))->setRequired(),
      'id_owner' => (new Lookup($this, $this->translate('Owner'), User::class))->setDefaultValue($this->main->auth->getUserId()),
      'id_manager' => (new Lookup($this, $this->translate('Manager'), User::class))->setDefaultValue($this->main->auth->getUserId()),
      'customer_order_number' => (new Varchar($this, $this->translate('Customer\' order number')))->setProperty('defaultVisibility', true),
      'id_pipeline' => (new Lookup($this, $this->translate('Pipeline'), Pipeline::class))->setDefaultValue(1),
      'id_pipeline_step' => (new Lookup($this, $this->translate('Pipeline step'), PipelineStep::class))->setDefaultValue(null),
      'shared_folder' => new Varchar($this, "Shared folder (online document storage)"),
      'note' => (new Text($this, $this->translate('Notes'))),
      'source_channel' => (new Integer($this, $this->translate('Source channel')))->setEnumValues(self::ENUM_SOURCE_CHANNELS),
      'is_closed' => (new Boolean($this, $this->translate('Closed'))),
      'is_archived' => (new Boolean($this, $this->translate('Archived')))->setDefaultValue(false),
      'deal_result' => (new Integer($this, $this->translate('Deal Result')))
        ->setEnumValues(self::ENUM_DEAL_RESULTS)
        ->setEnumCssClasses([
          self::RESULT_UNKNOWN => 'bg-yellow-100 text-yellow-800',
          self::RESULT_WON => 'bg-green-100 text-green-800',
          self::RESULT_LOST => 'bg-red-100 text-red-800',
        ])
        ->setDefaultValue(self::RESULT_UNKNOWN)
      ,
      'lost_reason' => (new Lookup($this, $this->translate("Reason for Lost"), LostReason::class)),
      'date_result_update' => (new DateTime($this, $this->translate('Date of result update')))->setReadonly(),
      'is_new_customer' => new Boolean($this, $this->translate('New Customer')),
      'business_type' => (new Integer($this, $this->translate('Business type')))
        ->setEnumValues(self::ENUM_BUSINESS_TYPES)
        ->setEnumCssClasses([
          self::BUSINESS_TYPE_NEW => 'bg-yellow-100 text-yellow-800',
          self::BUSINESS_TYPE_EXISTING => 'bg-blue-100 text-blue-800',
        ])
        ->setDefaultValue(self::BUSINESS_TYPE_NEW)
      ,
      'date_created' => (new DateTime($this, $this->translate('Created')))->setRequired()->setReadonly()->setDefaultValue(date("Y-m-d H:i:s")),
    ]);
  }

  public function describeInput(string $columnName): \Hubleto\Framework\Description\Input
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

  public function describeTable(): \Hubleto\Framework\Description\Table
  {
    $description = parent::describeTable();
    if ($this->main->urlParamAsBool("showArchive")) {
      $description->permissions = [
        "canCreate" => false,
        "canUpdate" => false,
        "canRead" => true,
        "canDelete" => $this->main->permissions->granted($this->fullName . ':Delete')
      ];
    } else {
      $description->ui['addButtonText'] = $this->translate('Add Deal');
    }
    $description->ui['showHeader'] = true;
    $description->ui['showFulltextSearch'] = true;
    $description->ui['showColumnSearch'] = true;
    $description->ui['showFooter'] = false;
    $description->ui['defaultFilters'] = [
      'fDealSourceChannel' => [ 'title' => $this->translate('Source channel'), 'type' => 'multipleSelectButtons', 'options' => self::ENUM_SOURCE_CHANNELS ],
      'fDealResult' => [ 'title' => $this->translate('Result'), 'type' => 'multipleSelectButtons', 'options' => self::ENUM_DEAL_RESULTS ],
      'fDealBusinessType' => [ 'title' => $this->translate('Business type'), 'options' => array_merge([ 0 => $this->translate('All')], self::ENUM_BUSINESS_TYPES) ],
      'fDealOwnership' => [ 'title' => $this->translate('Ownership'), 'options' => [ 0 => $this->translate('All'), 1 => $this->translate('Owned by me'), 2 => $this->translate('Managed by me') ] ],
      'fDealClosed' => [ 'title' => $this->translate('Open / Closed'), 'options' => [ 0 => $this->translate('Open'), 1 => $this->translate('Closed') ] ],
      'fDealArchive' => [ 'title' => $this->translate('Archived'), 'options' => [ 0 => $this->translate('Active'), 1 => $this->translate('Archived') ] ],
    ];

    unset($description->columns['note']);
    unset($description->columns['id_contact']);
    unset($description->columns['source_channel']);
    unset($description->columns['is_archived']);
    unset($description->columns['id_lead']);
    unset($description->columns['id_pipeline']);
    unset($description->columns['shared_folder']);
    unset($description->columns['date_result_update']);
    unset($description->columns['lost_reason']);
    unset($description->columns['is_new_customer']);
    unset($description->columns['business_type']);

    return $description;
  }

  public function describeForm(): \Hubleto\Framework\Description\Form
  {
    $mSettings = $this->main->di->create(Setting::class);
    $defaultCurrency = (int) $mSettings->record
      ->where("key", "Apps\Community\Settings\Currency\DefaultCurrency")
      ->first()
      ->value
    ;

    $description = parent::describeForm();
    $description->defaultValues['id_currency'] = $defaultCurrency;

    return $description;
  }

  public function onAfterCreate(array $savedRecord): array
  {
    $savedRecord = parent::onAfterCreate($savedRecord);

    $mDealHistory = $this->main->di->create(DealHistory::class);
    $mDealHistory->record->recordCreate([
      "change_date" => date("Y-m-d"),
      "id_deal" => $savedRecord["id"],
      "description" => $this->translate("Deal created")
    ]);

    $newDeal = $savedRecord;
    if (empty($newDeal['identifier'])) {
      $newDeal["identifier"] = $this->main->apps->community('Deals')->configAsString('dealPrefix') . str_pad($savedRecord["id"], 6, 0, STR_PAD_LEFT);
      $this->record->recordUpdate($newDeal);
    }

    return $savedRecord;
  }

  public function onAfterUpdate(array $originalRecord, array $savedRecord): array
  {
    $savedRecord = parent::onAfterUpdate($originalRecord, $savedRecord);

    if (isset($savedRecord["TAGS"])) {
      $helper = new Helper($this->main);
      $helper->deleteTags(
        array_column($savedRecord["TAGS"], "id"),
        "HubletoApp/Community/Deals/Models/DealTag",
        "id_deal",
        $savedRecord["id"]
      );
    }

    $sums = 0;
    $calculator = new CalculatePrice($this->main);
    $allProducts = array_merge($savedRecord["PRODUCTS"] ?? [], $savedRecord["SERVICES"] ?? []);

    if (!empty($allProducts)) {
      foreach ($allProducts as $product) {
        if (!isset($product["_toBeDeleted_"])) {
          $sums += $calculator->calculatePriceIncludingVat(
            $product["unit_price"],
            $product["amount"],
            $product["vat"] ?? 0,
            $product["discount"] ?? 0
          );
        }
      }
      $this->record->find($savedRecord["id"])->update(["price" => $sums]);
    }

    return $savedRecord;
  }

  public function getOwnership(array $record): void
  {
    if (isset($record["id_customer"]) && !isset($record["checkOwnership"])) {
      $mCustomer = $this->main->di->create(Customer::class);
      $customer = $mCustomer->record
        ->where("id", $record["id_customer"])
        ->first()
      ;

      // if (isset($record['id_owner']) && $customer->id_owner != $record["id_owner"]) {
      //   throw new \Exception("This deal cannot be assigned to the selected user,\nbecause they are not assigned to the selected customer.");
      // }
    }
  }

  public function onBeforeCreate(array $record): array
  {
    $this->getOwnership($record);
    return $record;
  }

  public function onBeforeUpdate(array $record): array
  {
    $oldRecord = $this->record->find($record["id"])->toArray();
    $mDealHistory = $this->main->di->create(DealHistory::class);

    $diff = $this->diffRecords($oldRecord, $record);
    $columns = $this->getColumns();
    foreach ($diff as $columnName => $values) {
      $oldValue = $values[0] ?? "None";
      $newValue = $values[1] ?? "None";

      if ($columns[$columnName]->getType() == "lookup") {
        $lookupModel = $this->main->getModel($columns[$columnName]->getLookupModel());
        $lookupSqlValue = $lookupModel->getLookupSqlValue($lookupModel->table);

        if ($oldValue != "None") {
          $oldValue = $lookupModel->record
            ->selectRaw($lookupSqlValue)
            ->where("id", $values[0])
            ->first()->toArray()
          ;
          $oldValue = reset($oldValue);
        }

        if ($newValue != "None") {
          $newValue = $lookupModel->record
            ->selectRaw($lookupSqlValue)
            ->where("id", $values[1])
            ->first()->toArray()
          ;
          $newValue = reset($newValue);
        }

        $mDealHistory->record->recordCreate([
          "change_date" => date("Y-m-d"),
          "id_deal" => $record["id"],
          "description" => $columns[$columnName]->getTitle() . " changed from " . $oldValue . " to " . $newValue,
        ]);
      } else {
        if ($columns[$columnName]->getType() == "boolean") {
          $oldValue = $values[0] ? "Yes" : "No";
          $newValue = $values[1] ? "Yes" : "No";
        } elseif (!empty($columns[$columnName]->getEnumValues())) {
          $oldValue = $columns[$columnName]->getEnumValues()[$oldValue] ?? "None";
          $newValue = $columns[$columnName]->getEnumValues()[$newValue] ?? "None";
        }

        $mDealHistory->record->recordCreate([
          "change_date" => date("Y-m-d"),
          "id_deal" => $record["id"],
          "description" => $columns[$columnName]->getTitle() . $this->translate(" changed from ") . $oldValue . $this->translate(" to ") . $newValue,
        ]);
      }
    }

    return $record;
  }
}
