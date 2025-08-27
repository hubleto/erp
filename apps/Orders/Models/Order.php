<?php

namespace HubletoApp\Community\Orders\Models;

use DateTimeImmutable;
use Hubleto\Framework\Db\Column\Date;
use Hubleto\Framework\Db\Column\Boolean;
use Hubleto\Framework\Db\Column\Decimal;
use Hubleto\Framework\Db\Column\Lookup;
use Hubleto\Framework\Db\Column\Text;
use Hubleto\Framework\Db\Column\Varchar;
use HubletoApp\Community\Customers\Models\Customer;
use HubletoApp\Community\Products\Models\Product;
use HubletoApp\Community\Settings\Models\Currency;
use HubletoApp\Community\Settings\Models\Setting;

use HubletoApp\Community\Documents\Generator;
use HubletoApp\Community\Documents\Models\Template;
use HubletoApp\Community\Projects\Models\ProjectOrder;
use HubletoApp\Community\Pipeline\Models\Pipeline;
use HubletoApp\Community\Pipeline\Models\PipelineStep;
use HubletoApp\Community\Invoices\Models\Invoice;
use HubletoApp\Community\Invoices\Models\Dto\Invoice as InvoiceDto;
use HubletoApp\Community\Settings\Models\User;

class Order extends \Hubleto\Erp\Model
{
  public string $table = 'orders';
  public string $recordManagerClass = RecordManagers\Order::class;
  public ?string $lookupSqlValue = 'concat(ifnull({%TABLE%}.identifier, ""), " ", ifnull({%TABLE%}.title, ""))';

  public array $relations = [
    'CUSTOMER' => [ self::HAS_ONE, Customer::class, 'id','id_customer'],
    'OWNER' => [ self::BELONGS_TO, User::class, 'id_owner', 'id'],
    'MANAGER' => [ self::BELONGS_TO, User::class, 'id_manager', 'id'],
    'CURRENCY' => [ self::HAS_ONE, Currency::class, 'id', 'id_currency'],
    'PIPELINE' => [ self::HAS_ONE, Pipeline::class, 'id', 'id_pipeline'],
    'PIPELINE_STEP' => [ self::HAS_ONE, PipelineStep::class, 'id', 'id_pipeline_step'],
    'TEMPLATE' => [ self::HAS_ONE, Template::class, 'id', 'id_template'],

    'PRODUCTS' => [ self::HAS_MANY, OrderProduct::class, 'id_order', 'id' ],
    'DOCUMENTS' => [ self::HAS_MANY, OrderDocument::class, 'id_order', 'id' ],
    'INVOICES' => [ self::HAS_MANY, OrderInvoice::class, 'id_order', 'id' ],
    'HISTORY' => [ self::HAS_MANY, History::class, 'id_order', 'id' ],
    'DEALS' => [ self::HAS_MANY, OrderDeal::class, 'id_order', 'id' ],

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
      'identifier' => (new Varchar($this, $this->translate('Identifier')))->setCssClass('badge badge-info')->setProperty('defaultVisibility', true),
      'title' => (new Varchar($this, $this->translate('Title')))->setRequired()->setProperty('defaultVisibility', true),
      'id_customer' => (new Lookup($this, $this->translate('Customer'), Customer::class))->setRequired()->setProperty('defaultVisibility', true),
      'title' => (new Varchar($this, $this->translate('Title')))->setCssClass('font-bold')->setProperty('defaultVisibility', true),
      'id_owner' => (new Lookup($this, $this->translate('Owner'), User::class))->setDefaultValue($this->getAuthProvider()->getUserId()),
      'id_manager' => (new Lookup($this, $this->translate('Manager'), User::class))->setDefaultValue($this->getAuthProvider()->getUserId()),
      'id_pipeline' => (new Lookup($this, $this->translate('Pipeline'), Pipeline::class))->setDefaultValue(1),
      'id_pipeline_step' => (new Lookup($this, $this->translate('Pipeline step'), PipelineStep::class))->setDefaultValue(null)->setProperty('defaultVisibility', true),
      'price_excl_vat' => (new Decimal($this, $this->translate('Price excl. VAT')))->setDefaultValue(0)->setProperty('defaultVisibility', true),
      'price_incl_vat' => (new Decimal($this, $this->translate('Price incl. VAT')))->setDefaultValue(0)->setProperty('defaultVisibility', true),
      'id_currency' => (new Lookup($this, $this->translate('Currency'), Currency::class))->setReadonly(),
      'date_order' => (new Date($this, $this->translate('Order date')))->setRequired()->setDefaultValue(date("Y-m-d")),
      'required_delivery_date' => (new Date($this, $this->translate('Required delivery date'))),
      'shipping_info' => (new Varchar($this, $this->translate('Shipping information'))),
      'note' => (new Text($this, $this->translate('Notes'))),
      'id_template' => (new Lookup($this, $this->translate('Template'), Template::class)),
      'is_closed' => (new Boolean($this, $this->translate('Closed')))->setProperty('defaultVisibility', true),
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

    unset($description->columns["shipping_info"]);
    unset($description->columns["note"]);

    $description->ui['defaultFilters'] = [
      'fOrderPipelineStep' => Pipeline::buildTableDefaultFilterForPipelineSteps($this, 'Stage'),
    ];

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

    $mOrderProduct = $this->getService(OrderProduct::class);
    $allProducts = $mOrderProduct->record->where('id_order', $savedRecord['id'])->get()->toArray();

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
   * [Description for onAfterCreate]
   *
   * @param array $savedRecord
   * 
   * @return array
   * 
   */
  public function onAfterCreate(array $savedRecord): array
  {
    $mPipeline = $this->getService(Pipeline::class);

    list($defaultPipeline, $idPipeline, $idPipelineStep) = $mPipeline->getDefaultPipelineInGroup('orders');

    $savedRecord['id_pipeline'] = $idPipeline;
    $savedRecord['id_pipeline_step'] = $idPipelineStep;

    $this->record->recordUpdate($savedRecord);

    $savedRecord = parent::onAfterCreate($savedRecord);

    $order = $this->record->find($savedRecord["id"]);
    $order->identifier = $order->id;
    $order->save();

    $mHistory = $this->getService(History::class);
    $mHistory->record->recordCreate([
      "id_order" => $order->id,
      "short_description" => "Order created",
      "date_time" => date("Y-m-d H:i:s"),
    ]);

    return $savedRecord;
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

    $generator = $this->getService(Generator::class);
    $idDocument = $generator->generatePdfFromTemplate(
      $template->id,
      'order-' . $order->id . '-' . new DateTimeImmutable()->format('Ymd-His') . '.pdf',
      $order->toArray()
    );

    if ($idDocument > 0) {
      $mOrderDocument = $this->getService(OrderDocument::class);
      $mOrderDocument->record->recordCreate([
        'id_order' => $idOrder,
        'id_document' => $idDocument,
      ]);
    }

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
    $mInvoice = $this->getService(Invoice::class);

    $order = $this->record->prepareReadQuery()->where('id', $idOrder)->first();

    $idInvoice = 0;

    if ($order) {
      $idInvoice = $mInvoice->generateInvoice(new InvoiceDto(
        1, // $idProfile
        $this->getAuthProvider()->getUserId(), // $idIssuedBy
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
