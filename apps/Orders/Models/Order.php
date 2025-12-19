<?php

namespace Hubleto\App\Community\Orders\Models;

use DateTimeImmutable;

use Hubleto\App\Community\Orders\Loader as OrdersApp;

use Hubleto\Framework\Db\Column\Date;
use Hubleto\Framework\Db\Column\Boolean;
use Hubleto\Framework\Db\Column\Decimal;
use Hubleto\Framework\Db\Column\Lookup;
use Hubleto\Framework\Db\Column\Text;
use Hubleto\Framework\Db\Column\Varchar;
use Hubleto\Framework\Db\Column\File;
use Hubleto\Framework\Db\Column\Integer;
use Hubleto\App\Community\Customers\Models\Customer;
use Hubleto\App\Community\Settings\Models\Currency;
use Hubleto\App\Community\Settings\Models\Setting;

use Hubleto\App\Community\Documents\Generator;
use Hubleto\App\Community\Documents\Models\Template;
use Hubleto\App\Community\Projects\Models\ProjectOrder;
use Hubleto\App\Community\Workflow\Models\Workflow;
use Hubleto\App\Community\Workflow\Models\WorkflowStep;
use Hubleto\App\Community\Invoices\Models\Invoice;
use Hubleto\App\Community\Invoices\Models\Dto\Invoice as InvoiceDto;
use Hubleto\App\Community\Auth\Models\User;
use Hubleto\App\Community\Suppliers\Models\Supplier;

class Order extends \Hubleto\Erp\Model
{
  const PURCHASE_ORDER = 1;
  const SALES_ORDER = 2;

  public string $table = 'orders';
  public string $recordManagerClass = RecordManagers\Order::class;
  public ?string $lookupSqlValue = 'concat(ifnull({%TABLE%}.identifier, ""), " ", ifnull({%TABLE%}.title, ""))';
  public ?string $lookupUrlAdd = 'orders/add';
  public ?string $lookupUrlDetail = 'orders/{%ID%}';

  public array $relations = [
    'CUSTOMER' => [ self::HAS_ONE, Customer::class, 'id', 'id_customer'],
    'SUPPLIER' => [ self::HAS_ONE, Supplier::class, 'id', 'id_supplier'],
    'OWNER' => [ self::BELONGS_TO, User::class, 'id_owner', 'id'],
    'MANAGER' => [ self::BELONGS_TO, User::class, 'id_manager', 'id'],
    'CURRENCY' => [ self::HAS_ONE, Currency::class, 'id', 'id_currency'],
    'WORKFLOW' => [ self::HAS_ONE, Workflow::class, 'id', 'id_workflow'],
    'WORKFLOW_STEP' => [ self::HAS_ONE, WorkflowStep::class, 'id', 'id_workflow_step'],
    'TEMPLATE' => [ self::HAS_ONE, Template::class, 'id', 'id_template'],

    'ITEMS' => [ self::HAS_MANY, Item::class, 'id_order', 'id' ],
    'DOCUMENTS' => [ self::HAS_MANY, OrderDocument::class, 'id_order', 'id' ],
    'HISTORY' => [ self::HAS_MANY, History::class, 'id_order', 'id' ],
    'DEALS' => [ self::HAS_MANY, OrderDeal::class, 'id_order', 'id' ],
    'ACTIVITIES' => [ self::HAS_MANY, OrderActivity::class, 'id_deal', 'id' ],

    'PROJECTS' => [ self::HAS_MANY, ProjectOrder::class, 'id_order', 'id' ],
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
      'purchase_sales' => (new Integer($this, $this->translate('Order type')))->setEnumValues([
        self::PURCHASE_ORDER => $this->translate('Purchase order'),
        self::SALES_ORDER => $this->translate('Sales order'),
      ])->setEnumCssClasses([
        self::PURCHASE_ORDER => 'bg-lime-50 text-slate-700',
        self::SALES_ORDER => 'bg-yellow-50 text-slate-700',
      ])->setDefaultValue(self::PURCHASE_ORDER)->setDefaultVisible(),
      'identifier' => (new Varchar($this, $this->translate('Identifier')))->setCssClass('badge badge-info')->setDefaultVisible()->setIcon(self::COLUMN_IDENTIFIER_DEFUALT_ICON),
      'identifier_external' => (new Varchar($this, $this->translate('Identifier external')))->setDefaultVisible(),
      'title' => (new Varchar($this, $this->translate('Title')))->setRequired()->setDefaultVisible()->setCssClass('font-bold')->setIcon(self::COLUMN_NAME_DEFAULT_ICON),
      'id_customer' => (new Lookup($this, $this->translate('Customer'), Customer::class))->setDefaultVisible()->setIcon(self::COLUMN_ID_CUSTOMER_DEFAULT_ICON),
      'id_supplier' => (new Lookup($this, $this->translate('Supplier'), Supplier::class))->setDefaultVisible()->setIcon(self::COLUMN_ID_SUPPLIER_DEFAULT_ICON),
      'id_owner' => (new Lookup($this, $this->translate('Owner'), User::class))->setReactComponent('InputUserSelect')->setDefaultValue($this->getService(\Hubleto\Framework\AuthProvider::class)->getUserId()),
      'id_manager' => (new Lookup($this, $this->translate('Manager'), User::class))->setReactComponent('InputUserSelect')->setDefaultValue($this->getService(\Hubleto\Framework\AuthProvider::class)->getUserId()),
      'id_workflow' => (new Lookup($this, $this->translate('Workflow'), Workflow::class)),
      'id_workflow_step' => (new Lookup($this, $this->translate('Workflow step'), WorkflowStep::class))->setDefaultVisible(),
      'price_excl_vat' => (new Decimal($this, $this->translate('Price excl. VAT')))->setDefaultValue(0)->setDefaultVisible(),
      'price_incl_vat' => (new Decimal($this, $this->translate('Price incl. VAT')))->setDefaultValue(0)->setDefaultVisible(),
      'id_currency' => (new Lookup($this, $this->translate('Currency'), Currency::class))->setReadonly(),
      'date_order' => (new Date($this, $this->translate('Order date')))->setRequired()->setDefaultValue(date("Y-m-d")),
      'required_delivery_date' => (new Date($this, $this->translate('Required delivery date'))),
      'shipping_info' => (new Varchar($this, $this->translate('Shipping information'))),
      'note' => (new Text($this, $this->translate('Notes'))),
      'shared_folder' => new Varchar($this, $this->translate("Shared folder (online document storage)"))
        ->setReactComponent('InputHyperlink')
        ->setDescription($this->translate('Link to shared folder (online storage) with related documents'))
      ,
      'id_template' => (new Lookup($this, $this->translate('Template'), Template::class)),
      'pdf' => (new File($this, $this->translate('PDF'))),
      'is_closed' => (new Boolean($this, $this->translate('Closed')))->setDefaultVisible(),
    ]);
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

    $description->ui['title'] = ''; // 'Orders';
    $description->ui['addButtonText'] = $this->translate("Add order");

    $description->show(['header', 'fulltextSearch', 'columnSearch', 'moreActionsButton']);
    $description->hide(['footer']);

    unset($description->columns["shipping_info"]);
    unset($description->columns["note"]);

    $description->ui['filters'] = [
      'fOrderWorkflowStep' => Workflow::buildTableFilterForWorkflowSteps($this, 'Stage'),
      'fOrderClosed' => [
        'title' => $this->translate('Open / Closed'),
        'options' => [
          0 => $this->translate('Open'),
          1 => $this->translate('Closed'),
          2 => $this->translate('All'),
        ],
        'default' => 0,
      ],
    ];

    $view = $this->router()->urlParamAsString("view");

    $description->addFilter('fPurchaseSales', [
      'title' => $this->translate('Purchase / Sales'),
      'options' => [
        0 => $this->translate('All'),
        self::PURCHASE_ORDER => $this->translate('Purchase orders'),
        self::SALES_ORDER => $this->translate('Sales orders'),
      ],
      'default' => ($view == 'purchaseOrders' ? self::PURCHASE_ORDER : ($view == 'salesOrders' ? self::SALES_ORDER : 0)),
    ]);

    $fCustomerOptions = [];
    foreach ($this->record->groupBy('id_customer')->with('CUSTOMER')->get() as $value) {
      if ($value->CUSTOMER) $fCustomerOptions[$value->id] = $value->CUSTOMER->name;
    }
    $description->addFilter('fOrderCustomer', [
      'title' => $this->translate('Customer'),
      'type' => 'multipleSelectButtons',
      'options' => $fCustomerOptions,
    ]);
    
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

    /** @var Setting */
    $mSettings = $this->getModel(Setting::class);

    $defaultCurrency = (int) $mSettings->record
      ->where("key", "Apps\Community\Settings\Currency\DefaultCurrency")
      ->first()
      ->value
    ;

    $description = parent::describeForm();
    $description->defaultValues["id_currency"] = $defaultCurrency;

    return $description;
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

    $totalExclVat = 0;
    $totalInclVat = 0;

    $mItem = $this->getService(Item::class);
    $allItems = $mItem->record->where('id_order', $savedRecord['id'])->get()->toArray();

    if (!empty($allItems)) {
      foreach ($allItems as $item) {
        if (!isset($item["_toBeDeleted_"])) {
          $totalExclVat += $item['price_excl_vat'];
          $totalInclVat += $item['price_incl_vat'];
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
   * [Description for onAfterCreate]
   *
   * @param array $savedRecord
   * 
   * @return array
   * 
   */
  public function onAfterCreate(array $savedRecord): array
  {
    /** @var Workflow */
    $mWorkflow = $this->getModel(Workflow::class);

    list($defaultWorkflow, $idWorkflow, $idWorkflowStep) = $mWorkflow->getDefaultWorkflowInGroup('orders');

    $savedRecord['id_workflow'] = $idWorkflow;
    $savedRecord['id_workflow_step'] = $idWorkflowStep;

    $this->record->recordUpdate($savedRecord);

    $savedRecord = parent::onAfterCreate($savedRecord);

    $order = $this->record->find($savedRecord["id"]);

    if (empty($savedRecord['identifier'])) {

      $identifier = $this->config()->forApp(OrdersApp::class)->getAsString('numberingPattern', 'O{YY}-{#}');
      $identifier = str_replace('{YYYY}', date('Y'), $identifier);
      $identifier = str_replace('{YY}', date('y'), $identifier);
      $identifier = str_replace('{MM}', date('m'), $identifier);
      $identifier = str_replace('{DD}', date('d'), $identifier);
      $identifier = str_replace('{#}', $savedRecord['id'], $identifier);

      $savedRecord["identifier"] = $identifier;
      $this->record->recordUpdate($savedRecord);
    }

    $mHistory = $this->getService(History::class);
    $mHistory->record->recordCreate([
      "id_order" => $order->id,
      "short_description" => "Order created",
      "date_time" => date("Y-m-d H:i:s"),
    ]);

    return $savedRecord;
  }

  public function getPreviewVars(int $idOrder): array
  {
    /** @var Order */
    $mOrder = $this->getModel(Order::class);

    $order = $mOrder->record->prepareReadQuery()->where('orders.id', $idOrder)->first();
    if (!$order) throw new \Exception('Order was not found.');

    $vars = $order->toArray();
    $vars['now'] = new \DateTimeImmutable()->format('Y-m-d H:i:s');

    unset($vars['CUSTOMER']['CONTACTS']);
    unset($vars['CUSTOMER']['OWNER']);
    unset($vars['CUSTOMER']['MANAGER']);
    unset($vars['CUSTOMER']['ACTIVITIES']);
    unset($vars['CUSTOMER']['DOCUMENTS']);
    unset($vars['CUSTOMER']['TAGS']);
    unset($vars['CUSTOMER']['LEADS']);
    unset($vars['CUSTOMER']['DEALS']);
    unset($vars['PROFILE']['COMPANY']);
    unset($vars['PROFILE']['TEMPLATE']);
    unset($vars['OWNER']['ROLES']);
    unset($vars['OWNER']['TEAMS']);
    unset($vars['OWNER']['DEFAULT_COMPANY']);
    unset($vars['MANAGER']['ROLES']);
    unset($vars['MANAGER']['TEAMS']);
    unset($vars['MANAGER']['DEFAULT_COMPANY']);
    unset($vars['WORKFLOW']);
    unset($vars['WORKFLOW_STEP']);
    unset($vars['TEMPLATE']);

    return $vars;

  }

  public function getPreviewHtml(int $idOrder): string
  {

    $vars = $this->getPreviewVars($idOrder);

    /** @var Template */
    $mTemplate = $this->getService(Template::class);

    $template = $mTemplate->record->prepareReadQuery()->where('documents_templates.id', $vars['id_template'])->first();
    if (!$template) throw new \Exception('Template was not found.');


    /** @var Generator */
    $generator = $this->getService(Generator::class);
    return $generator->renderTemplate($vars['id_template'], $vars);
  }

  /**
   * Generates PDF document from given order and returns ID of generated document
   *
   * @param int $idOrder Order for which the PDF should be generated.
   * 
   * @return int ID of generated document.
   * 
   */
  public function generatePdf(int $idOrder): int
  {
    $mOrder = $this->getService(Order::class);
    $order = $mOrder->record->prepareReadQuery()->where('orders.id', $idOrder)->first();
    if (!$order) throw new \Exception('Order was not found.');

    $mTemplate = $this->getService(Template::class);
    $template = $mTemplate->record->prepareReadQuery()->where('documents_templates.id', $order->id_template)->first();
    if (!$template) throw new \Exception('Template was not found.');

    $vars = $this->getPreviewVars($idOrder);

    $orderOutputFilename = 'order-' . $order->id . '-' . new DateTimeImmutable()->format('Ymd-His') . '.pdf';

    $generator = $this->getService(Generator::class);
    $idDocument = $generator->createPdfFromTemplate(
      $template->id,
      $orderOutputFilename,
      $vars
    );

    $mOrder->record->find($idOrder)->update([
      'pdf' => $orderOutputFilename,
    ]);

    return $idDocument;
  }

  /**
   * Generates invoice for given order.
   *
   * @param int $idOrder
   * 
   * @return void
   * 
   */
  public function generateInvoice(int $idOrder): int
  {
    /** @var Invoice */
    $mInvoice = $this->getModel(Invoice::class);

    $order = $this->record->prepareReadQuery()->where('id', $idOrder)->first();

    $idInvoice = 0;

    if ($order) {
      $idInvoice = $mInvoice->generateInvoice(new InvoiceDto(
        1, // $idProfile
        $this->getService(\Hubleto\Framework\AuthProvider::class)->getUserId(), // $idIssuedBy
        (int) $order['id_customer'], // $idCustomer
        'ORD/' . $order->number, // $number
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
