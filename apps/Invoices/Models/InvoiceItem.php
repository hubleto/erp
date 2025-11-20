<?php

namespace Hubleto\App\Community\Invoices\Models;

use Hubleto\App\Community\Invoices\PriceCalculator;
use Hubleto\App\Community\Orders\Models\Order;
use Hubleto\Framework\Db\Column\Lookup;
use Hubleto\Framework\Db\Column\Varchar;
use Hubleto\Framework\Db\Column\Decimal;

use Hubleto\App\Community\Orders\Models\OrderProduct;

class InvoiceItem extends \Hubleto\Erp\Model
{

  public string $table = 'invoice_items';
  public ?string $lookupSqlValue = '{%TABLE%}.id_invoice';
  public string $recordManagerClass = RecordManagers\InvoiceItem::class;

  public array $relations = [
    'INVOICE' => [ self::BELONGS_TO, Invoice::class, "id_invoice" ],
    'ORDER' => [ self::BELONGS_TO, Order::class, "id_order" ],
    'ORDER_PRODUCT' => [ self::BELONGS_TO, OrderProduct::class, "id_order_product" ],
  ];

  public function describeColumns(): array
  {
    return array_merge(parent::describeColumns(), [
      'id_invoice' => (new Lookup($this, $this->translate('Invoice'), Invoice::class))->setRequired(),
      'id_order' => (new Lookup($this, $this->translate('Order'), OrderProduct::class))->setRequired(),
      'id_order_product' => (new Lookup($this, $this->translate('Order Product'), OrderProduct::class))->setRequired(),
      'item' => (new Varchar($this, $this->translate('Item')))->setRequired(),
      'unit_price' => new Decimal($this, $this->translate('Unit price')),
      'amount' => new Decimal($this, $this->translate('Amount')),
      'discount' => new Decimal($this, $this->translate('Discount')),
      'vat' => new Decimal($this, $this->translate('VAT'))->setUnit('%'),
      'price_excl_vat' => new Decimal($this, $this->translate('Price excl. VAT'))->setReadonly(),
      'price_incl_vat' => new Decimal($this, $this->translate('Price incl. VAT'))->setReadonly(),
    ]);
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