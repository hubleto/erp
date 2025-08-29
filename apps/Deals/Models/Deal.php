<?php

namespace Hubleto\App\Community\Deals\Models;

use Hubleto\Framework\Db\Column\Boolean;
use Hubleto\Framework\Db\Column\Date;
use Hubleto\Framework\Db\Column\DateTime;
use Hubleto\Framework\Db\Column\Decimal;
use Hubleto\Framework\Db\Column\Integer;
use Hubleto\Framework\Db\Column\Lookup;
use Hubleto\Framework\Db\Column\Text;
use Hubleto\Framework\Db\Column\Varchar;
use Hubleto\App\Community\Contacts\Models\Contact;
use Hubleto\App\Community\Customers\Models\Customer;
use Hubleto\App\Community\Leads\Models\Lead;
use Hubleto\App\Community\Products\Controllers\Api\CalculatePrice;
use Hubleto\App\Community\Settings\Models\Currency;
use Hubleto\App\Community\Pipeline\Models\Pipeline;
use Hubleto\App\Community\Pipeline\Models\PipelineStep;
use Hubleto\App\Community\Settings\Models\Setting;
use Hubleto\App\Community\Settings\Models\User;
use Hubleto\Framework\Helper;

use Hubleto\App\Community\Documents\Generator;
use Hubleto\App\Community\Documents\Models\Template;
use Hubleto\App\Community\Orders\Models\OrderDeal;
use Hubleto\App\Community\Invoices\Models\Invoice;
use Hubleto\App\Community\Invoices\Models\Dto\Invoice as InvoiceDto;

class Deal extends \Hubleto\Erp\Model
{
  public string $table = 'deals';
  public string $recordManagerClass = RecordManagers\Deal::class;
  public ?string $lookupSqlValue = 'concat(ifnull({%TABLE%}.identifier, ""), " ", ifnull({%TABLE%}.title, ""))';
  public ?string $lookupUrlDetail = 'deals/{%ID%}';

  public const RESULT_UNKNOWN = 0;
  public const RESULT_WON = 1;
  public const RESULT_LOST = 2;

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
    'TEMPLATE_QUOTATION' => [ self::HAS_ONE, Template::class, 'id', 'id_template_quotation'],

    'HISTORY' => [ self::HAS_MANY, DealHistory::class, 'id_deal', 'id'],
    'TAGS' => [ self::HAS_MANY, DealTag::class, 'id_deal', 'id' ],
    'PRODUCTS' => [ self::HAS_MANY, DealProduct::class, 'id_deal', 'id' ],
    'ACTIVITIES' => [ self::HAS_MANY, DealActivity::class, 'id_deal', 'id' ],
    'DOCUMENTS' => [ self::HAS_MANY, DealDocument::class, 'id_deal', 'id'],
    'LEADS' => [ self::HAS_MANY, DealLead::class, 'id_deal', 'id'],
    'TASKS' => [ self::HAS_MANY, DealTask::class, 'id_deal', 'id'],

    // 'ORDERS' => [ self::HAS_MANY, OrderDeal::class, 'id_deal', 'id'],
  ];

  /**
   * [Description for describeColumns]
   *
   * @return array
   * 
   */
  public function describeColumns(): array
  {
    return array_merge(parent::describeColumns(), [
      'identifier' => (new Varchar($this, $this->translate('Deal Identifier')))->setCssClass('badge badge-info')->setProperty('defaultVisibility', true),
      'title' => (new Varchar($this, $this->translate('Title')))->setRequired()->setProperty('defaultVisibility', true)->setCssClass('font-bold'),
      'id_customer' => (new Lookup($this, $this->translate('Customer'), Customer::class))->setDefaultValue($this->getRouter()->urlParamAsInteger('idCustomer')),
      'id_contact' => (new Lookup($this, $this->translate('Contact'), Contact::class)),
      'id_lead' => (new Lookup($this, $this->translate('Lead'), Lead::class))->setReadonly(),
      'version' => (new Integer($this, $this->translate('Version'))),
      // 'price' => (new Decimal($this, $this->translate('Price')))->setDecimals(2),
      'price_excl_vat' => new Decimal($this, $this->translate('Price excl. VAT')),
      'price_incl_vat' => new Decimal($this, $this->translate('Price incl. VAT')),
      'id_currency' => (new Lookup($this, $this->translate('Currency'), Currency::class))->setFkOnUpdate('RESTRICT')->setFkOnDelete('SET NULL')->setReadonly(),
      'date_expected_close' => (new Date($this, $this->translate('Expected close date'))),
      'id_owner' => (new Lookup($this, $this->translate('Owner'), User::class))->setDefaultValue($this->getAuthProvider()->getUserId()),
      'id_manager' => (new Lookup($this, $this->translate('Manager'), User::class))->setDefaultValue($this->getAuthProvider()->getUserId()),
      'id_template_quotation' => (new Lookup($this, $this->translate('Template for quotation'), Template::class)),
      'customer_order_number' => (new Varchar($this, $this->translate('Customer\'s order number')))->setProperty('defaultVisibility', true),
      'id_pipeline' => (new Lookup($this, $this->translate('Pipeline'), Pipeline::class)),
      'id_pipeline_step' => (new Lookup($this, $this->translate('Pipeline step'), PipelineStep::class))->setProperty('defaultVisibility', true),
      'shared_folder' => new Varchar($this, "Shared folder (online document storage)"),
      'note' => (new Text($this, $this->translate('Notes'))),
      'source_channel' => (new Integer($this, $this->translate('Source channel')))->setEnumValues(self::ENUM_SOURCE_CHANNELS),
      'is_closed' => (new Boolean($this, $this->translate('Closed')))->setProperty('defaultVisibility', true),
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

  /**
   * [Description for describeInput]
   *
   * @param string $columnName
   * 
   * @return \Hubleto\Framework\Description\Input
   * 
   */
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

  /**
   * [Description for describeTable]
   *
   * @return \Hubleto\Framework\Description\Table
   * 
   */
  public function describeTable(): \Hubleto\Framework\Description\Table
  {
    $description = parent::describeTable();
    if ($this->getRouter()->urlParamAsBool("showArchive")) {
      $description->permissions = [
        "canCreate" => false,
        "canUpdate" => false,
        "canRead" => true,
        "canDelete" => $this->getPermissionsManager()->granted($this->fullName . ':Delete')
      ];
    } else {
      $description->ui['addButtonText'] = $this->translate('Add Deal');
    }
    $description->ui['showHeader'] = true;
    $description->ui['showFulltextSearch'] = true;
    $description->ui['showColumnSearch'] = true;
    $description->ui['showFooter'] = false;
    $description->ui['filters'] = [
      'fDealPipelineStep' => Pipeline::buildTableFilterForPipelineSteps($this, 'State'),
      'fDealSourceChannel' => [ 'title' => $this->translate('Source channel'), 'type' => 'multipleSelectButtons', 'options' => self::ENUM_SOURCE_CHANNELS ],
      'fDealOwnership' => [ 'title' => $this->translate('Ownership'), 'options' => [ 0 => $this->translate('All'), 1 => $this->translate('Owned by me'), 2 => $this->translate('Managed by me') ] ],
      'fDealClosed' => [
        'title' => $this->translate('Open / Closed'),
        'options' => [
          0 => $this->translate('Open'),
          1 => $this->translate('Closed'),
          2 => $this->translate('All'),
        ],
        'default' => 0,
      ],
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

  /**
   * [Description for describeForm]
   *
   * @return \Hubleto\Framework\Description\Form
   * 
   */
  public function describeForm(): \Hubleto\Framework\Description\Form
  {
    $mSettings = $this->getService(Setting::class);
    $defaultCurrency = (int) $mSettings->record
      ->where("key", "Apps\Community\Settings\Currency\DefaultCurrency")
      ->first()
      ->value
    ;

    $description = parent::describeForm();
    $description->defaultValues['id_currency'] = $defaultCurrency;

    return $description;
  }

  /**
   * [Description for onAfterCreate]
   *
   * @param array $savedRecord
   * 
   * @return array
   * 
   */
  public function onAfterCreate(array $savedRecord): array
  {
    $savedRecord = parent::onAfterCreate($savedRecord);

    $mDealHistory = $this->getService(DealHistory::class);
    $mDealHistory->record->recordCreate([
      "change_date" => date("Y-m-d"),
      "id_deal" => $savedRecord["id"],
      "description" => $this->translate("Deal created")
    ]);

    $newDeal = $savedRecord;
    if (empty($newDeal['identifier'])) {
      $newDeal["identifier"] = $this->getAppManager()->getApp(\Hubleto\App\Community\Deals\Loader::class)->configAsString('dealPrefix') . str_pad($savedRecord["id"], 6, 0, STR_PAD_LEFT);
      $this->record->recordUpdate($newDeal);
    }

    return $savedRecord;
  }

  /**
   * [Description for onAfterUpdate]
   *
   * @param array $originalRecord
   * @param array $savedRecord
   * 
   * @return array
   * 
   */
  public function onAfterUpdate(array $originalRecord, array $savedRecord): array
  {
    $savedRecord = parent::onAfterUpdate($originalRecord, $savedRecord);

    if (isset($savedRecord["TAGS"])) {
      $helper = $this->getService(Helper::class);
      $helper->deleteTags(
        array_column($savedRecord["TAGS"], "id"),
        $this->getModel("Hubleto/App/Community/Deals/Models/DealTag"),
        "id_deal",
        $savedRecord["id"]
      );
    }

    $totalExclVat = 0;
    $totalInclVat = 0;

    $mDealProduct = $this->getService(DealProduct::class);
    $allProducts = $mDealProduct->record->where('id_deal', $savedRecord['id'])->get()->toArray();

    if (!empty($allProducts)) {
      foreach ($allProducts as $product) {
        if (!isset($product["_toBeDeleted_"])) {
          $totalExclVat += $product['price_excl_vat'];
          $totalInclVat += $product['price_incl_vat'];
        }
      }

      $this->record->find($savedRecord["id"])->update([
        "price_excl_vat" => $totalExclVat,
        "price_incl_vat" => $totalInclVat,
      ]);
    }

    return $savedRecord;
  }

  /**
   * [Description for onBeforeUpdate]
   *
   * @param array $record
   * 
   * @return array
   * 
   */
  public function onBeforeUpdate(array $record): array
  {
    $oldRecord = $this->record->find($record["id"])->toArray();
    $mDealHistory = $this->getService(DealHistory::class);

    $diff = $this->diffRecords($oldRecord, $record);
    $columns = $this->getColumns();
    foreach ($diff as $columnName => $values) {
      $oldValue = $values[0] ?? "None";
      $newValue = $values[1] ?? "None";

      if ($columns[$columnName]->getType() == "lookup") {
        $lookupModel = $this->getModel($columns[$columnName]->getLookupModel());
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

  /**
   * Generates quotation PDF document from given deal and returns ID of generated document
   *
   * @param int $idDeal Deal for which the PDF should be generated.
   * 
   * @return int ID of generated document.
   * 
   */
  public function generateQuotationPdf(int $idDeal): int
  {
    $mDeal = $this->getService(Deal::class);
    $deal = $mDeal->record->prepareReadQuery()->where('deals.id', $idDeal)->first();
    if (!$deal) throw new \Exception('Deal was not found.');

    $mTemplate = $this->getService(Template::class);
    $template = $mTemplate->record->prepareReadQuery()->where('documents_templates.id', $deal->id_template_quotation)->first();
    if (!$template) throw new \Exception('Template was not found.');

    $vars = $deal->toArray();
    $vars['now'] = new \DateTimeImmutable()->format('Y-m-d H:i:s');

    $generator = $this->getService(Generator::class);
    $idDocument = $generator->generatePdfFromTemplate(
      $template->id,
      'quotation-' . Helper::str2url($deal->identifier) . ($deal->version ? '-v' . $deal->version : '') . '-' . date('YmdHis') . '.pdf',
      $vars
    );

    if ($idDocument > 0) {
      $mDealDocument = $this->getService(DealDocument::class);
      $mDealDocument->record->recordCreate([
        'id_deal' => $idDeal,
        'id_document' => $idDocument,
      ]);
    }

    return $idDocument;
  }

  /**
   * Generates invoice for given deal.
   *
   * @param int $idDeal
   * 
   * @return void
   * 
   */
  public function generateInvoice(int $idDeal): int
  {
    $mInvoice = $this->getService(Invoice::class);

    $deal = $this->record->prepareReadQuery()->where('id', $idDeal)->first();

    $idInvoice = 0;

    if ($deal) {
      $idInvoice = $mInvoice->generateInvoice(new InvoiceDto(
        1, // $idProfile
        $this->getAuthProvider()->getUserId(), // $idIssuedBy
        (int) $deal['id_customer'], // $idCustomer
        'ORD/' . $deal->number, // $number
        null, // $vs
        '', // $cs
        '', // $ss
        null, // $dateIssue
        new \DateTimeImmutable()->add(new \DateInterval('P14D')), // $dateDelivery
        new \DateTimeImmutable()->add(new \DateInterval('P14D')), // $dateDue
        null, // $datePayment
        '', // $note
      ));
    }

    return $idInvoice;
  }
}
