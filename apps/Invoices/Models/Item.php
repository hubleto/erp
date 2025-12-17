<?php

namespace Hubleto\App\Community\Invoices\Models;

use Hubleto\App\Community\Invoices\PriceCalculator;
use Hubleto\App\Community\Orders\Models\Order;
use Hubleto\Framework\Db\Column\Lookup;
use Hubleto\Framework\Db\Column\Varchar;
use Hubleto\Framework\Db\Column\Decimal;
use Hubleto\App\Community\Customers\Models\Customer;

use Hubleto\App\Community\Orders\Models\OrderProduct;

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
    'ORDER_PRODUCT' => [ self::BELONGS_TO, OrderProduct::class, "id_order_product" ],
  ];

  public function describeColumns(): array
  {
    return array_merge(parent::describeColumns(), [
      'id_invoice' => (new Lookup($this, $this->translate('Invoice'), Invoice::class))->setRequired(),
      'id_customer' => (new Lookup($this, $this->translate('Customer'), Customer::class))->setDefaultVisible()->setIcon(self::COLUMN_ID_CUSTOMER_DEFAULT_ICON)->setRequired(),
      'id_order' => (new Lookup($this, $this->translate('Order'), Order::class))->setDefaultVisible(),
      'id_order_product' => (new Lookup($this, $this->translate('Order Product'), OrderProduct::class))->setDefaultVisible(),
      'item' => (new Varchar($this, $this->translate('Item')))->setRequired()->setDefaultVisible(),
      'unit_price' => new Decimal($this, $this->translate('Unit price'))->setDefaultVisible(),
      'amount' => new Decimal($this, $this->translate('Amount'))->setDefaultVisible(),
      'discount' => new Decimal($this, $this->translate('Discount'))->setDefaultVisible(),
      'vat' => new Decimal($this, $this->translate('VAT'))->setUnit('%')->setDefaultVisible(),
      'price_excl_vat' => new Decimal($this, $this->translate('Price excl. VAT'))->setReadonly(),
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
    $description = parent::describeTable();

    $description->ui['title'] = ''; //$this->translate('Customers');
    $description->ui['addButtonText'] = $this->translate('Add invoice item');
    $description->show(['header', 'fulltextSearch', 'columnSearch', 'moreActionsButton']);
    $description->hide(['footer']);

    $description->addFilter('fStatus', [
      'title' => $this->translate('Status'),
      'options' => [
        0 => $this->translate('All'),
        1 => $this->translate('Not invoiced'),
        2 => $this->translate('Invoiced')
      ]
    ]);

    return $description;
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

    $calculator = $this->getService(PriceCalculator::class);
    $item = $this->record->find($idItem)->first();

    $priceExclVat = $calculator->calculatePriceExcludingVat(
      (float) $item->unit_price,
      (float) $item->amount,
      (float) $item->discount
    );
    $priceInclVat = $calculator->calculatePriceIncludingVat(
      (float) $item->unit_price,
      (float) $item->amount,
      (float) $item->vat,
      (float) $item->discount
    );

    $this->record->find($idItem)->update([
      "price_excl_vat" => $priceExclVat,
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