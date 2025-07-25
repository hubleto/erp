<?php

namespace HubletoApp\Community\Deals\Models;

use Hubleto\Framework\Db\Column\Decimal;
use Hubleto\Framework\Db\Column\Integer;
use Hubleto\Framework\Db\Column\Lookup;
use HubletoApp\Community\Products\Controllers\Api\CalculatePrice;
use HubletoApp\Community\Products\Models\Product;

class DealProduct extends \Hubleto\Framework\Models\Model
{
  public string $table = 'deal_products';
  public string $recordManagerClass = RecordManagers\DealProduct::class;
  public ?string $lookupSqlValue = '{%TABLE%}.id_product';

  public array $relations = [
    'PRODUCT' => [ self::BELONGS_TO, Product::class, 'id_product', 'id' ],
    'DEAL' => [ self::BELONGS_TO, Deal::class, 'id_deal', 'id' ],
  ];

  public function describeColumns(): array
  {
    return array_merge(parent::describeColumns(), [
      'id_deal' => (new Lookup($this, $this->translate('Deal'), Deal::class))->setRequired(),
      'id_product' => (new Lookup($this, $this->translate('Product'), Product::class))->setFkOnUpdate("CASCADE")->setFkOnDelete("SET NULL")->setRequired(),
      'unit_price' => (new Decimal($this, $this->translate('Unit Price')))->setRequired(),
      'amount' => (new Integer($this, $this->translate('Amount')))->setRequired(),
      'vat' => (new Decimal($this, $this->translate('Vat')))->setUnit("%"),
      'discount' => (new Decimal($this, $this->translate('Discount')))->setUnit("%"),
      'sum' => new Decimal($this, $this->translate('Sum')),
    ]);
  }

  public function describeTable(): \Hubleto\Framework\Description\Table
  {
    $description = parent::describeTable();
    if ($this->main->urlParamAsInteger('idDeal') > 0) {
      // $description->permissions = [
      //   'canRead' => $this->main->permissions->granted($this->fullName . ':Read'),
      //   'canCreate' => $this->main->permissions->granted($this->fullName . ':Create'),
      //   'canUpdate' => $this->main->permissions->granted($this->fullName . ':Update'),
      //   'canDelete' => $this->main->permissions->granted($this->fullName . ':Delete'),
      // ];
      $description->columns = [];
      $description->inputs = [];
      $description->ui = [];
    }

    return $description;
  }

  public function onBeforeCreate(array $record): array
  {
    $record["sum"] = (new CalculatePrice($this->main))->calculatePriceIncludingVat(
      $record["unit_price"],
      $record["amount"],
      $record["vat"] ?? 0,
      $record["discount"] ?? 0
    );
    return $record;
  }
  public function onBeforeUpdate(array $record): array
  {
    $record["sum"] = (new CalculatePrice($this->main))->calculatePriceIncludingVat(
      $record["unit_price"],
      $record["amount"],
      $record["vat"] ?? 0,
      $record["discount"] ?? 0
    );
    return $record;
  }
}
