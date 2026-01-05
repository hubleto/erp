<?php

namespace Hubleto\App\Community\Invoices\Models;

use Hubleto\App\Community\Invoices\PriceCalculator;
use Hubleto\App\Community\Orders\Models\Order;
use Hubleto\Framework\Db\Column\Lookup;
use Hubleto\Framework\Db\Column\Varchar;
use Hubleto\Framework\Db\Column\Decimal;
use Hubleto\App\Community\Customers\Models\Customer;

use Hubleto\App\Community\Orders\Models\Item as OrderItem;

class Item extends \Hubleto\Erp\Model
{

  public string $table = 'invoice_items';
  public string $recordManagerClass = RecordManagers\Item::class;
  public ?string $lookupSqlValue = '{%TABLE%}.item';
  public ?string $lookupUrlAdd = 'invoices/items/add';
  public ?string $lookupUrlDetail = 'invoices/items/{%ID%}';

  public array $relations = [
    'INVOICE' => [ self::BELONGS_TO, Invoice::class, "id_invoice" ],
    'CUSTOMER' => [ self::BELONGS_TO, Customer::class, "id_customer" ],
    'ORDER' => [ self::BELONGS_TO, Order::class, "id_order" ],
    'ORDER_ITEM' => [ self::BELONGS_TO, OrderItem::class, "id_order_item" ],
  ];

  public function describeColumns(): array
  {
    return array_merge(parent::describeColumns(), [
      'id_invoice' => (new Lookup($this, $this->translate('Invoice'), Invoice::class))->setRequired(),
      'id_customer' => (new Lookup($this, $this->translate('Customer'), Customer::class))->setDefaultVisible()->setIcon(self::COLUMN_ID_CUSTOMER_DEFAULT_ICON),
      'id_order' => (new Lookup($this, $this->translate('Order'), Order::class))->setDefaultVisible(),
      'id_order_item' => (new Lookup($this, $this->translate('Order Item'), OrderItem::class))->setDefaultVisible(),
      'item' => (new Varchar($this, $this->translate('Item')))->setRequired()->setDefaultVisible(),
      'unit_price' => new Decimal($this, $this->translate('Unit price'))->setDefaultVisible(),
      'amount' => new Decimal($this, $this->translate('Amount'))->setDefaultVisible(),
      'discount' => new Decimal($this, $this->translate('Discount'))->setDefaultVisible(),
      'vat' => new Decimal($this, $this->translate('VAT'))->setUnit('%')->setDefaultVisible(),
      'price_excl_vat' => new Decimal($this, $this->translate('Price excl. VAT'))->setReadonly(),
      'price_vat' => new Decimal($this, $this->translate('VAT'))->setReadonly(),
      'price_incl_vat' => new Decimal($this, $this->translate('Price incl. VAT'))->setReadonly(),
    ]);
  }

  public function upgrades(): array {
    return [
      1 => "ALTER TABLE `invoice_items` DROP FOREIGN KEY `fk_8a5dbd71fda9248d8ece4f8146fc29af`",
      2 => "ALTER TABLE `invoice_items` ADD CONSTRAINT `fk_8a5dbd71fda9248d8ece4f8146fc29af` FOREIGN KEY (`id_order`) REFERENCES `orders`(`id`) ON DELETE RESTRICT ON UPDATE RESTRICT",
    ];
  }

  public function describeTable(): \Hubleto\Framework\Description\Table
  {
    $view = $this->router()->urlParamAsString("view");
    $filters = $this->router()->urlParamAsArray("filters");

    $description = parent::describeTable();

    $description->ui['title'] = ''; //$this->translate('Customers');
    $description->ui['addButtonText'] = $this->translate('Add invoice item');
    $description->show(['header', 'fulltextSearch', 'columnSearch', 'moreActionsButton']);
    $description->show(['footer']);

    if (isset($filters['fGroupBy'])) {
      $fGroupBy = (array) $filters['fGroupBy'];

      $showOnlyColumns = [];
      if (in_array('customer', $fGroupBy)) $showOnlyColumns[] = 'id_customer';
      if (in_array('order', $fGroupBy)) $showOnlyColumns[] = 'id_order';
      if (in_array('invoice', $fGroupBy)) $showOnlyColumns[] = 'id_invoice';

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

    $description->addFilter('fStatus', [
      'title' => $this->translate('Status'),
      'options' => [
        1 => $this->translate('Prepared'),
        2 => $this->translate('Invoiced')
      ]
    ]);

    $description->addFilter('fPeriod', [
      'title' => $this->translate('Period'),
      'options' => [
        'today' => $this->translate('Today'),
        'yesterday' => $this->translate('Yesterday'),
        'last7Days' => $this->translate('Last 7 days'),
        'last14Days' => $this->translate('Last 14 days'),
        'thisMonth' => $this->translate('This month'),
        'lastMonth' => $this->translate('Last month'),
        'beforeLastMonth' => $this->translate('Month before last'),
        'thisYear' => $this->translate('This year'),
        'lastYear' => $this->translate('Last year'),
      ],
      'default' => 0,
    ]);

    $description->addFilter('fGroupBy', [
      'title' => $this->translate('Group by'),
      'type' => 'multipleSelectButtons',
      'options' => [
        'customer' => $this->translate('Customer'),
        'order' => $this->translate('Order'),
        'invoice' => $this->translate('Invoice'),
      ]
    ]);

    return $description;
  }

  public function getRelationsIncludedInLoadTableData(): array|null
  {
    return ['INVOICE'];
  }

  public function getMaxReadLevelForLoadTableData(): int
  {
    return 1;
  }

  /**
   * [Description for recalculatePrices]
   *
   * @param int $idInvoice
   * 
   * @return void
   * 
   */
  public function recalculatePrices(int $idItem): void
  {
    if ($idItem <= 0) return;

    /** @var PriceCalculator */
    $calculator = $this->getService(PriceCalculator::class);
    $item = $this->record->where('invoice_items.id', $idItem)->first();

    $priceExclVat = $calculator->calculatePriceExcludingVat(
      (float) $item->unit_price,
      (float) $item->amount,
      (float) $item->discount
    );
    $priceVat = $calculator->calculateVat(
      $priceExclVat,
      (float) $item->vat
    );
    $priceInclVat = $calculator->calculatePriceIncludingVat(
      (float) $item->unit_price,
      (float) $item->amount,
      (float) $item->vat,
      (float) $item->discount
    );

    $this->record->find($idItem)->update([
      "price_excl_vat" => $priceExclVat,
      "price_vat" => $priceVat,
      "price_incl_vat" => $priceInclVat,
    ]);
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

    $this->recalculatePrices((int) $savedRecord['id']);

    /** @var Invoice */
    $mInvoice = $this->getModel(Invoice::class);
    $mInvoice->recalculateTotalsForInvoice((int) $savedRecord['id_invoice']);

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

    $this->recalculatePrices((int) $savedRecord['id']);

    /** @var Invoice */
    $mInvoice = $this->getModel(Invoice::class);
    $mInvoice->recalculateTotalsForInvoice((int) $savedRecord['id_invoice']);

    return $savedRecord;
  }

}