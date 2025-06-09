<?php

namespace HubletoApp\Community\Deals\Models;

use ADIOS\Core\Db\Column\Boolean;
use ADIOS\Core\Db\Column\Date;
use ADIOS\Core\Db\Column\DateTime;
use ADIOS\Core\Db\Column\Decimal;
use ADIOS\Core\Db\Column\Integer;
use ADIOS\Core\Db\Column\Lookup;
use ADIOS\Core\Db\Column\Text;
use ADIOS\Core\Db\Column\Varchar;

use HubletoApp\Community\Contacts\Models\Contact;
use HubletoApp\Community\Customers\Models\Customer;
use HubletoApp\Community\Leads\Models\Lead;
use HubletoApp\Community\Products\Controllers\Api\CalculatePrice;
use HubletoApp\Community\Settings\Models\Currency;
use HubletoApp\Community\Pipeline\Models\Pipeline;
use HubletoApp\Community\Pipeline\Models\PipelineStep;
use HubletoApp\Community\Settings\Models\Setting;
use HubletoApp\Community\Settings\Models\User;
use HubletoMain\Core\Helper;

class Deal extends \HubletoMain\Core\Models\Model
{
  public string $table = 'deals';
  public string $recordManagerClass = RecordManagers\Deal::class;
  public ?string $lookupSqlValue = '{%TABLE%}.title';
  public ?string $lookupUrlDetail = 'deals/{%ID%}';

  // public array $rolePermissions = [
  //   \HubletoApp\Community\Settings\Models\UserRole::ROLE_CHIEF_OFFICER => [ true, true, true, true ],
  //   \HubletoApp\Community\Settings\Models\UserRole::ROLE_MANAGER => [ true, true, true, true ],
  //   \HubletoApp\Community\Settings\Models\UserRole::ROLE_EMPLOYEE => [ true, true, true, false ],
  //   \HubletoApp\Community\Settings\Models\UserRole::ROLE_ASSISTANT => [ true, true, false, false ],
  //   \HubletoApp\Community\Settings\Models\UserRole::ROLE_EXTERNAL => [ false, false, false, false ],
  // ];

  const RESULT_PENDING = 1;
  const RESULT_WON = 2;
  const RESULT_LOST = 3;

  const BUSINESS_TYPE_NEW = 1;
  const BUSINESS_TYPE_EXISTING = 2;

  public array $relations = [
    'LEAD' => [ self::BELONGS_TO, Lead::class, 'id_lead', 'id'],
    'CUSTOMER' => [ self::BELONGS_TO, Customer::class, 'id_customer', 'id' ],
    'OWNER' => [ self::BELONGS_TO, User::class, 'id_owner', 'id'],
    'RESPONSIBLE' => [ self::BELONGS_TO, User::class, 'id_responsible', 'id'],
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
      'identifier' => (new Varchar($this, $this->translate('Deal Identifier'))),
      'title' => (new Varchar($this, $this->translate('Title')))->setRequired(),
      'id_customer' => (new Lookup($this, $this->translate('Customer'), Customer::class))->setFkOnUpdate('CASCADE')->setFkOnDelete('RESTRICT')->setRequired(),
      'id_contact' => (new Lookup($this, $this->translate('Contact'), Contact::class))->setFkOnUpdate('CASCADE')->setFkOnDelete('SET NULL'),
      'id_lead' => (new Lookup($this, $this->translate('Lead'), Lead::class))->setFkOnUpdate('CASCADE')->setFkOnDelete('SET NULL')->setReadonly(),
      'price' => (new Decimal($this, $this->translate('Price'))),
      'id_currency' => (new Lookup($this, $this->translate('Currency'), Currency::class))->setFkOnUpdate('RESTRICT')->setFkOnDelete('SET NULL')->setReadonly(),
      'date_expected_close' => (new Date($this, $this->translate('Expected close date')))->setRequired(),
      'id_owner' => (new Lookup($this, $this->translate('Owner'), User::class)),
      'id_responsible' => (new Lookup($this, $this->translate('Responsible'), User::class)),
      'date_created' => (new DateTime($this, $this->translate('Created')))->setRequired()->setReadonly(),
      'id_pipeline' => (new Lookup($this, $this->translate('Pipeline'), Pipeline::class))->setFkOnUpdate('CASCADE')->setFkOnDelete('SET NULL'),
      'id_pipeline_step' => (new Lookup($this, $this->translate('Pipeline step'), PipelineStep::class))->setFkOnUpdate('CASCADE')->setFkOnDelete('SET NULL'),
      'shared_folder' => new Varchar($this, "Shared folder (online document storage)"),
      'note' => (new Text($this, $this->translate('Notes'))),
      'source_channel' => (new Integer($this, $this->translate('Source channel')))->setEnumValues([
        1 => "Advertissment",
        2 => "Partner",
        3 => "Web",
        4 => "Cold call",
        5 => "E-mail",
        6 => "Refferal",
        7 => "Other",
      ]),
      'is_archived' => (new Boolean($this, $this->translate('Archived'))),
      'deal_result' => (new Integer($this, $this->translate('Deal Result')))
        ->setEnumValues([$this::RESULT_PENDING => "Pending", $this::RESULT_WON => "Won", $this::RESULT_LOST => "Lost"])->setDefaultValue(3),
      'lost_reason' => (new Lookup($this, $this->translate("Reason for Lost"), LostReason::class)),
      'date_result_update' => (new DateTime($this, $this->translate('Date of result update')))->setReadonly(),
      'is_new_customer' => new Boolean($this, $this->translate('New Customer')),
      'business_type' => (new Integer($this, $this->translate('Business type')))->setEnumValues(
        [$this::BUSINESS_TYPE_NEW => "New", $this::BUSINESS_TYPE_EXISTING => "Existing"]
      ),
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
      case 'deal_result':
          $description->setEnumCssClasses([
            1 => "!text-white-500",
            2 => "!text-green-500",
            3 => "!text-red-500",
          ]);
        break;
      default:
        break;
    }
    return $description;
  }

  public function describeTable(): \ADIOS\Core\Description\Table
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
    // $description->columns['tags'] = ["title" => "Tags"];
    unset($description->columns['note']);
    unset($description->columns['id_contact']);
    unset($description->columns['source_channel']);
    unset($description->columns['is_archived']);
    unset($description->columns['id_lead']);
    unset($description->columns['id_pipeline']);
    unset($description->columns['shared_folder']);
    unset($description->columns['date_result_update']);
    unset($description->columns['lost_reason']);

    // if ($this->main->urlParamAsInteger('idCustomer') > 0) {
    //   $description->permissions = [
    //     'canRead' => $this->main->permissions->granted($this->fullName . ':Read'),
    //     'canCreate' => $this->main->permissions->granted($this->fullName . ':Create'),
    //     'canUpdate' => $this->main->permissions->granted($this->fullName . ':Update'),
    //     'canDelete' => $this->main->permissions->granted($this->fullName . ':Delete'),
    //   ];
    //   $description->columns = [];
    //   $description->inputs = [];
    //   $description->ui = [];
    // }

    return $description;
  }

  public function describeForm(): \ADIOS\Core\Description\Form
  {
    $mSettings = new Setting($this->main);
    $defaultPipeline = 1;
    $defaultCurrency = (int) $mSettings->record
      ->where("key", "Apps\Community\Settings\Currency\DefaultCurrency")
      ->first()
      ->value
    ;

    $description = parent::describeForm();
    // $description->defaultValues['is_new_customer'] = 0;
    $description->defaultValues['id_customer'] = $this->main->urlParamAsInteger('idCustomer');
    $description->defaultValues['deal_result'] = $this::RESULT_PENDING;
    $description->defaultValues['business_type'] = $this::BUSINESS_TYPE_NEW;
    $description->defaultValues['is_archived'] = 0;
    $description->defaultValues['date_created'] = date("Y-m-d H:i:s");
    $description->defaultValues['id_currency'] = $defaultCurrency;
    $description->defaultValues['id_pipeline'] = $defaultPipeline;
    $description->defaultValues['id_pipeline_step'] = null;
    $description->defaultValues['id_owner'] = $this->main->auth->getUserId();
    $description->defaultValues['id_responsible'] = $this->main->auth->getUserId();

    return $description;
  }

  public function onAfterCreate(array $originalRecord, array $savedRecord): array
  {
    $mDealHistory = new DealHistory($this->main);
    $mDealHistory->record->recordCreate([
      "change_date" => date("Y-m-d"),
      "id_deal" => $savedRecord["id"],
      "description" => "Deal created"
    ]);

    $newDeal = $savedRecord;
    if (empty($newDeal['identifier'])) {
      $newDeal["identifier"] = $this->main->apps->community('Deals')->configAsString('dealPrefix') . str_pad($savedRecord["id"], 6, 0, STR_PAD_LEFT);
      $this->record->recordUpdate($newDeal);
    }

    return parent::onAfterCreate($originalRecord, $savedRecord);
  }

  public function onAfterUpdate(array $originalRecord, array $savedRecord): array
  {
    if (isset($originalRecord["TAGS"])) {
      $helper = new Helper($this->main, $this->app);
      $helper->deleteTags(
        array_column($originalRecord["TAGS"], "id"),
        "HubletoApp/Community/Deals/Models/DealTag",
        "id_deal",
        $originalRecord["id"]
      );
    }

    $sums = 0;
    $calculator = new CalculatePrice($this->main);
    $allProducts = array_merge($originalRecord["PRODUCTS"], $originalRecord["SERVICES"]);

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
      $mCustomer = new Customer($this->main);
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
    $this->getOwnership($record);

    $deal = $this->record->find($record["id"]);
    $mDealHistory = new DealHistory($this->main);

    if (isset($record["deal_result"]) && $record["deal_result"] != $deal->deal_result) {
      $record["date_result_update"] = date("Y-m-d H:i:s");
    }

    if (isset($record["price"]) && (float) $deal->price != (float) $record["price"]) {
      $mDealHistory->record->recordCreate([
        "change_date" => date("Y-m-d"),
        "id_deal" => (int) ($record["id"] ?? 0),
        "description" => "Price changed to " . (string) ($record["price"] ?? '')
      ]);
    }
    if (isset($record["date_expected_close"]) && (string) $deal->date_expected_close != (string) $record["date_expected_close"]) {
      $mDealHistory->record->recordCreate([
        "change_date" => date("Y-m-d"),
        "id_deal" => $record["id"],
        "description" => "Expected Close Date changed to ".date("d.m.Y", (int) strtotime((string) $record["date_expected_close"]))
      ]);
    }

    return $record;
  }
}
