<?php

namespace Hubleto\App\Community\Orders\Models;

use DateTimeImmutable;

use Hubleto\App\Community\Orders\Loader as OrdersApp;

use Hubleto\Framework\Db\Column\Json;
use Hubleto\Framework\Db\Column\Date;
use Hubleto\Framework\Db\Column\Boolean;
use Hubleto\Framework\Db\Column\Decimal;
use Hubleto\Framework\Db\Column\Lookup;
use Hubleto\Framework\Db\Column\Text;
use Hubleto\Framework\Db\Column\Varchar;
use Hubleto\Framework\Db\Column\File;
use Hubleto\Framework\Db\Column\Integer;
use Hubleto\Framework\Db\Column\Virtual;
use Hubleto\App\Community\Customers\Models\Customer;
use Hubleto\App\Community\Settings\Models\Currency;
use Hubleto\App\Community\Settings\Models\Setting;

use Hubleto\App\Community\Documents\Models\Template;
use Hubleto\App\Community\Documents\Models\Document;
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
    'DOCUMENT' => [ self::HAS_ONE, Document::class, 'id', 'id_document'],

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
      'purchase_sales' => (new Integer($this, $this->translate('Purchase / Sales')))->setEnumValues([
        self::PURCHASE_ORDER => $this->translate('Purchase order'),
        self::SALES_ORDER => $this->translate('Sales order'),
      ])->setEnumCssClasses([
        self::PURCHASE_ORDER => 'bg-lime-50 text-slate-700',
        self::SALES_ORDER => 'bg-yellow-50 text-slate-700',
      ])->setDefaultValue(self::PURCHASE_ORDER)->setDefaultVisible(),
      'identifier' => (new Varchar($this, $this->translate('Identifier')))->setCssClass('badge badge-info')->setDefaultVisible()->setIcon(self::COLUMN_IDENTIFIER_DEFUALT_ICON),
      'identifier_external' => (new Varchar($this, $this->translate('External identifier')))->setDefaultVisible(),
      'title' => (new Varchar($this, $this->translate('Title')))->setRequired()->setDefaultVisible()->setCssClass('font-bold')->setIcon(self::COLUMN_NAME_DEFAULT_ICON),
      'id_customer' => (new Lookup($this, $this->translate('Customer'), Customer::class))->setDefaultVisible()->setIcon(self::COLUMN_ID_CUSTOMER_DEFAULT_ICON),
      'id_supplier' => (new Lookup($this, $this->translate('Supplier'), Supplier::class))->setDefaultVisible()->setIcon(self::COLUMN_ID_SUPPLIER_DEFAULT_ICON),
      'id_owner' => (new Lookup($this, $this->translate('Owner'), User::class))->setReactComponent('InputUserSelect')->setDefaultValue($this->getService(\Hubleto\Framework\AuthProvider::class)->getUserId()),
      'id_manager' => (new Lookup($this, $this->translate('Manager'), User::class))->setReactComponent('InputUserSelect')->setDefaultValue($this->getService(\Hubleto\Framework\AuthProvider::class)->getUserId()),
      'shared_with' => new Json($this, $this->translate('Shared with'), User::class)->setReactComponent('InputSharedWith')->setTableCellRenderer('TableCellRendererSharedWith'),
      'id_workflow' => (new Lookup($this, $this->translate('Workflow'), Workflow::class))->setReadonly(),
      'id_workflow_step' => (new Lookup($this, $this->translate('Workflow step'), WorkflowStep::class))->setDefaultVisible()->setReadonly(),
      'price_excl_vat' => (new Decimal($this, $this->translate('Price excl. VAT')))->setDefaultValue(0)->setDefaultVisible(),
      'price_incl_vat' => (new Decimal($this, $this->translate('Price incl. VAT')))->setDefaultValue(0)->setDefaultVisible(),
      'payment_period' => (new Integer($this, $this->translate('Payment period')))->setEnumValues([
        0 => $this->translate('No periodical payments'),
        1 => $this->translate('Monthly'),
        2 => $this->translate('Bi-Monthly'),
        3 => $this->translate('Quarterly'),
        6 => $this->translate('Each 6 months'),
        12 => $this->translate('Annually'),
        24 => $this->translate('Each 2 years'),
      ]),
      'prepaid_working_hours' => (new Integer($this, $this->translate('Prepaid working hours')))->setUnit('hours'),
      'prepaid_working_hours_period' => (new Integer($this, $this->translate('Prepaid working hours - period')))->setEnumValues([
        0 => $this->translate('No working hours recurrence'),
        1 => $this->translate('Monthly'),
        2 => $this->translate('Bi-Monthly'),
        3 => $this->translate('Quarterly'),
        6 => $this->translate('Each 6 months'),
        12 => $this->translate('Yearly'),
        24 => $this->translate('Each 2 years'),
      ]),
      'description_before' => (new Text($this, $this->translate('Description/notes before the list of items'))),
      'description_after' => (new Text($this, $this->translate('Description/notes after the list of items'))),
      'id_currency' => (new Lookup($this, $this->translate('Currency'), Currency::class))->setReadonly(),
      'date_order' => (new Date($this, $this->translate('Order date')))->setRequired()->setDefaultValue(date("Y-m-d")),
      'required_delivery_date' => (new Date($this, $this->translate('Required delivery date'))),
      'shipping_info' => (new Varchar($this, $this->translate('Shipping information'))),
      'note' => (new Text($this, $this->translate('Notes'))),
      'shared_folder' => (new Varchar($this, $this->translate("Shared folder (online document storage)"))->setCssClass('text-violet-800'))
        ->setReactComponent('InputHyperlink')
        ->setDescription($this->translate('Link to shared folder (online storage) with related documents'))
      ,
      'id_template' => (new Lookup($this, $this->translate('Template'), Template::class)),
      'id_document' => (new Lookup($this, $this->translate('Document'), Document::class)),
      'pdf' => (new File($this, $this->translate('PDF'))),
      'is_closed' => (new Boolean($this, $this->translate('Closed')))->setDefaultVisible(),
      'virt_last_item' => (new Virtual($this, $this->translate('Last item')))->setDefaultVisible()
        ->setProperty('sql', "
          SELECT
            JSON_OBJECT(
              'title', title,
              'unit_price', unit_price,
              'amount', amount,
              'date_due', date_due
            )
          FROM `orders_items` `oi`
          WHERE `oi`.`id_order` = `orders`.`id`
          ORDER BY `oi`.`date_due` desc
          LIMIT 1
        "),
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

    $view = $this->router()->urlParamAsString("view");
    $filters = $this->router()->urlParamAsArray("filters");

    $description->ui['title'] = ''; // 'Orders';
    $description->ui['addButtonText'] = $this->translate("Add order");

    $description->show(['header', 'fulltextSearch', 'columnSearch', 'moreActionsButton']);
    $description->hide(['footer']);
    $description->hideColumns(['shipping_info', 'note']);

    if (isset($filters['fGroupBy'])) {
      $fGroupBy = (array) $filters['fGroupBy'];

      $showOnlyColumns = [];
      if (in_array('customer', $fGroupBy)) $showOnlyColumns[] = 'id_customer';
      if (in_array('supplier', $fGroupBy)) $showOnlyColumns[] = 'id_supplier';
      if (in_array('manager', $fGroupBy)) $showOnlyColumns[] = 'id_manager';
      if (in_array('workflow_step', $fGroupBy)) $showOnlyColumns[] = 'id_workflow_step';

      $description->showOnlyColumns($showOnlyColumns);

      $description->addColumn(
        'total_price_excl_vat',
        (new Decimal($this, $this->translate('Total price excl. VAT')))->setDecimals(2)->setCssClass('badge badge-warning')
      );

      $description->addColumn(
        'total_price_incl_vat',
        (new Decimal($this, $this->translate('Total price incl. VAT')))->setDecimals(2)->setCssClass('badge badge-warning')
      );

    }

    $description->addFilter('fOrderWorkflowStep', Workflow::buildTableFilterForWorkflowSteps($this, 'Stage'));
    $description->addFilter('fOrderClosed', [
      'title' => $this->translate('Open / Closed'),
      'direction' => 'horizontal',
      'options' => [
        1 => $this->translate('Open'),
        2 => $this->translate('Closed'),
      ],
      'default' => 1,
    ]);

    $description->addFilter('fPurchaseSales', [
      'title' => $this->translate('Purchase / Sales'),
      'direction' => 'horizontal',
      'options' => [
        self::PURCHASE_ORDER => $this->translate('Purchase orders'),
        self::SALES_ORDER => $this->translate('Sales orders'),
      ],
      'default' => ($view == 'purchaseOrders' ? self::PURCHASE_ORDER : ($view == 'salesOrders' ? self::SALES_ORDER : 0)),
    ]);

    $description->addFilter('fGroupBy', [
      'title' => $this->translate('Group by'),
      'type' => 'multipleSelectButtons',
      'options' => [
        'customer' => $this->translate('Customer'),
        'supplier' => $this->translate('Supplier'),
        'manager' => $this->translate('Manager'),
        'workflow_step' => $this->translate('Workflow step'),
      ]
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
      ?->value
    ;

    $description = parent::describeForm();
    $description->defaultValues["id_currency"] = $defaultCurrency;

    return $description;
  }

  /**
   * [Description for getMaxReadLevelForLoadFormData]
   *
   * @return int
   * 
   */
  public function getMaxReadLevelForLoadFormData(): int
  {
    return 2;
  }

  /**
   * [Description for getLookupDetails]
   *
   * @param array $dataRaw
   * 
   * @return string
   * 
   */
  public function getLookupDetails(array $dataRaw): string
  {
    return $dataRaw['CUSTOMER']['name'] ?? '';
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
      "short_description" => $this->translate("Order created"),
      "date_time" => date("Y-m-d H:i:s"),
    ]);

    return $savedRecord;
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

  /**
   * [Description for getDocumentDefaultTemplate]
   *
   * @param array $vars
   * 
   * @return string
   * 
   */
  public function getDocumentDefaultTemplate(array $vars = []): string
  {
    return '
      <html>
      <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
        <title>Ponuka {{ user.DEFAULT_COMPANY.name }} {{ number }}</title>
        <style>
          {% if PDF_EXPORT %}
            * { font-family: Dejavu Sans !important; line-height: 0.95em; font-size: 10pt; }
          {% else %}
            * { font-family: Verdana !important; font-size: 10pt; }
          {% endif %} 
          .fbigger { font-size: 12pt; }
          table { width: 100%; border-collapse: collapse; }
          table td { vertical-align: top; }
          .dmain { border: 0.2mm solid #444444; margin-top: 0.5em; }
          .dcolumn { width: 50%; float: left; }
          .content { padding: 4pt; }
          .odberatel { border: 0.5mm solid #444444; }
          .dodod_info { color: #777; text-transform: uppercase; padding-bottom: 2mm; text-align: left; }
          .dodod_info span { border-bottom: 0.2mm solid #777; font-size: 8pt; font-style: italic; }
          .faktura_info { padding: 16pt 2pt 6pt 2pt; text-align: left; }
          table.polozky td { padding: 0.8pt; font-size: 9pt; vertical-align: middle; }
          table.polozky tr.header td { font-weight: bold; border-bottom: 0px solid #777; }
          table.polozky tr.sep td { width: 100%; height: 4pt; }
          .sumar { width: 100%; float: right; }
          .sumar table td { padding: 0.8pt; vertical-align: middle; }
          .sumar table tr.sep td { width: 100%; height: 10pt; }
          .sumar table tr.suma td { border-top: 0.2mm solid #777; padding-top: 1mm; }
          .text-right { text-align: right; }
        </style>
      </head>
      <body>
        <!-- TOP -->
        <div>Order # <b>{{ identifier }}</b></div>
        <br/>
        <div class="dmain">
          <div class="dcolumn">
            <div class="content">
              <div>
                <b>SUPPLIER</b><br/>
                <br/>
                <b>{{ user.DEFAULT_COMPANY.name }}</b><br/>
                {{ user.DEFAULT_COMPANY.street_1 }}<br/>
                {% if user.DEFAULT_COMPANY.street_2 %} {{ user.DEFAULT_COMPANY.street_2 }}<br/> {% endif %}
                {{ user.DEFAULT_COMPANY.zip }} {{ user.DEFAULT_COMPANY.city }}<br/>
                {% if user.DEFAULT_COMPANY.country %} {{ user.DEFAULT_COMPANY.country }}<br/> {% endif %}
                <br/>
                {% if user.DEFAULT_COMPANY.registration_id %} IČO: {{ user.DEFAULT_COMPANY.registration_id }}<br/> {% endif %}
                {% if user.DEFAULT_COMPANY.tax_id %} DIČ: {{ user.DEFAULT_COMPANY.tax_id }}<br/> {% endif %}
                {% if user.DEFAULT_COMPANY.vat_id %} IČDPH: {{ user.DEFAULT_COMPANY.vat_id }}<br/> {% endif %}
                <br/>
                {{ user.DEFAULT_COMPANY.business_register }}<br/>
              </div>
            </div>
          </div>
          <div class="dcolumn">
            <div class="content odberatel">
              <b>CUSTOMER</b><br/>
              <br/>
              <b>{{ CUSTOMER.name }}</b><br/>
              {{ CUSTOMER.street_line_1 }}<br/>
              {% if CUSTOMER.street_line_2 %} {{ CUSTOMER.street_line_2 }}<br/> {% endif %}
              {% if CUSTOMER.region %} {{ CUSTOMER.region }}<br/> {% endif %}
              {{ CUSTOMER.postal_code }} {{ CUSTOMER.city }}<br/>
              {% if CUSTOMER.country %} {{ CUSTOMER.country }}<br/> {% endif %}
              <br/>
              {% if CUSTOMER.customer_id %} IČO: {{ CUSTOMER.customer_id }}<br/> {% endif %}
              {% if CUSTOMER.tax_id %} DIČ: {{ CUSTOMER.tax_id }}<br/> {% endif %}
              {% if CUSTOMER.vat_id %} IČDPH: {{ CUSTOMER.vat_id }}<br/> {% endif %}
            </div>
          </div>
          <div style="clear:both;"></div>
        </div>

        {% if description_before %}
          <div style="text-align:left;margin:1em 0em;color:blue">{{ description_before }}</div>
        {% endif %}

        <div class="dmain">
          <div class="content">
            <table class="polozky">
              <thead>
                <tr class="header" style="background:#EEEEEE">
                  <td style="width:100%;padding:5pt 5pt 5pt 5pt">
                    <table style="width:100%">
                      <tbody>
                        <tr>
                          <td style="width:50%">Item</td>
                          <td style="width:50%;text-align:right">Unit price × Amount = Price</td>
                        </tr>
                      </tbody>
                    </table>
                  </td>
                </tr>
                <tr>
                  <td>&nbsp;</td>
                </tr>
              </thead>
              <tbody>
                {% for i in ITEMS %}
                  <tr>
                    <td style="width:100%;">
                      <table style="width:100%">
                        <tbody>
                          <tr>
                            <td style="width:50%;color:blue">
                              <i>{{ i.title }}</i>
                              <div style="color:#444444;font-size:8pt">{{ i.description }}<div>
                            </td>
                            <td style="width:50%;text-align:right">
                              <span>{{ hubleto.locale().formatCurrency(i.unit_price * (100 - i.discount)/100, CURRENCY.symbol) }}</span>
                              ×
                              {{ hubleto.locale().formatNumber(i.amount, 4) }}
                              = <b><span>{{ hubleto.locale().formatCurrency(i.price_excl_vat, CURRENCY.symbol) }}</span> excl. VAT</b>
                            </td>
                          </tr>
                        </tbody>
                      </table>
                    </td>
                  </tr>
                {% endfor %}
              </tbody>
            </table>
          </div>
        </div>

        {% if description_after %}
          <div style="text-align:left;margin:1em 0em;white-space:pre-wrap;">{{ description_after }}</div>
        {% endif %}

        <br/>
        <br/>
        Issued by {{ user.first_name }} {{ user.last_name }} {{ now }}.<br/>
        Generated in ERP Hubleto.
        https://www.hubleto.eu<br/>
      </body>
    ';
  }

}
