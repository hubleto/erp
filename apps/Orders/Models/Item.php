<?php

namespace Hubleto\App\Community\Orders\Models;

use Hubleto\Framework\Db\Column\Varchar;
use Hubleto\Framework\Db\Column\Decimal;
use Hubleto\Framework\Db\Column\Integer;
use Hubleto\Framework\Db\Column\Lookup;
use Hubleto\App\Community\Products\Models\Product;
use Hubleto\App\Community\Products\Controllers\Api\CalculatePrice;

class Item extends \Hubleto\Erp\Model
{
  public string $table = 'orders_items';
  public string $recordManagerClass = RecordManagers\Item::class;
  public ?string $lookupSqlValue = '{%TABLE%}.title';

  public array $relations = [
    'ORDER' => [ self::BELONGS_TO, Order::class, 'id_order', 'id'],
    'PRODUCT' => [ self::BELONGS_TO, Product::class, 'id_product', 'id'],
  ];

  public function describeColumns(): array
  {
    return array_merge(parent::describeColumns(), [
      'id_order' => (new Lookup($this, $this->translate('Order'), Order::class))->setRequired(),
      'position' => (new Integer($this, $this->translate('Position')))->setRequired()->setDefaultVisible(),
      'title' => (new Varchar($this, $this->translate('Title')))->setDefaultVisible()->setIcon(self::COLUMN_NAME_DEFAULT_ICON),
      'id_product' => (new Lookup($this, $this->translate('Product'), Product::class))->setDefaultVisible(),
      'sales_price' => (new Decimal($this, $this->translate('Sales price')))->setRequired()->setDefaultVisible(),
      'amount' => (new Integer($this, $this->translate('Amount')))->setRequired()->setDefaultVisible(),
      'discount' => (new Integer($this, $this->translate('Discount')))->setUnit('%')->setDefaultVisible(),
      'vat' => (new Integer($this, $this->translate('Vat')))->setUnit('%')->setDefaultVisible(),
      'price_excl_vat' => new Decimal($this, $this->translate('Price excl. VAT'))->setDefaultVisible(),
      'price_incl_vat' => new Decimal($this, $this->translate('Price incl. VAT'))->setDefaultVisible(),
    ]);
  }

  public function describeTable(): \Hubleto\Framework\Description\Table
  {
    $description = parent::describeTable();

    $description->ui['title'] = 'Order Items';
    $description->ui["addButtonText"] = $this->translate("Add item");

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
