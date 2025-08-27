<?php

namespace HubletoApp\Community\Orders\Models;

use Hubleto\Framework\Db\Column\Varchar;
use Hubleto\Framework\Db\Column\Decimal;
use Hubleto\Framework\Db\Column\Integer;
use Hubleto\Framework\Db\Column\Lookup;
use HubletoApp\Community\Products\Models\Product;
use HubletoApp\Community\Products\Controllers\Api\CalculatePrice;

class OrderProduct extends \Hubleto\Erp\Model
{
  public string $table = 'orders_products';
  public string $recordManagerClass = RecordManagers\OrderProduct::class;
  public ?string $lookupSqlValue = '{%TABLE%}.id';

  public array $relations = [
    'ORDER' => [ self::BELONGS_TO, Order::class, 'id_order', 'id'],
    'PRODUCT' => [ self::BELONGS_TO, Product::class, 'id_product', 'id'],
  ];

  public function describeColumns(): array
  {
    return array_merge(parent::describeColumns(), [
      'id_order' => (new Lookup($this, $this->translate('Order'), Order::class))->setRequired(),
      'order' => (new Integer($this, $this->translate('Order')))->setRequired()->setProperty('defaultVisibility', true),
      'title' => (new Varchar($this, $this->translate('Title')))->setProperty('defaultVisibility', true),
      'id_product' => (new Lookup($this, $this->translate('Product'), Product::class))->setProperty('defaultVisibility', true),
      'sales_price' => (new Decimal($this, $this->translate('Sales price')))->setRequired()->setProperty('defaultVisibility', true),
      'amount' => (new Integer($this, $this->translate('Amount')))->setRequired()->setProperty('defaultVisibility', true),
      'discount' => (new Integer($this, $this->translate('Discount')))->setUnit('%')->setProperty('defaultVisibility', true),
      'vat' => (new Integer($this, $this->translate('Vat')))->setUnit('%')->setProperty('defaultVisibility', true),
      'price_excl_vat' => new Decimal($this, $this->translate('Price excl. VAT'))->setProperty('defaultVisibility', true),
      'price_incl_vat' => new Decimal($this, $this->translate('Price incl. VAT'))->setProperty('defaultVisibility', true),
    ]);
  }

  public function describeTable(): \Hubleto\Framework\Description\Table
  {
    $description = parent::describeTable();

    $description->ui['title'] = 'Order Products';
    $description->ui["addButtonText"] = $this->translate("Add product");

    return $description;
  }

  public function onBeforeCreate(array $record): array
  {
    $record["price_excl_vat"] = ($this->getService(CalculatePrice::class))->calculatePriceExcludingVat(
      (float) ($record["sales_price"] ?? 0),
      (float) ($record["amount"] ?? 0),
      (float) ($record["discount"] ?? 0)
    );
    $record["price_incl_vat"] = ($this->getService(CalculatePrice::class))->calculatePriceIncludingVat(
      (float) ($record["sales_price"] ?? 0),
      (float) ($record["amount"] ?? 0),
      (float) ($record["vat"] ?? 0),
      (float) ($record["discount"] ?? 0)
    );
    return $record;
  }

  public function onBeforeUpdate(array $record): array
  {
    $record["price_excl_vat"] = ($this->getService(CalculatePrice::class))->calculatePriceExcludingVat(
      (float) ($record["sales_price"] ?? 0),
      (float) ($record["amount"] ?? 0),
      (float) ($record["discount"] ?? 0)
    );
    $record["price_incl_vat"] = ($this->getService(CalculatePrice::class))->calculatePriceIncludingVat(
      (float) ($record["sales_price"] ?? 0),
      (float) ($record["amount"] ?? 0),
      (float) ($record["vat"] ?? 0),
      (float) ($record["discount"] ?? 0)
    );
    return $record;
  }
}
