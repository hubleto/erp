<?php

namespace HubletoApp\Community\Leads\Models;

use ADIOS\Core\Db\Column\Decimal;
use ADIOS\Core\Db\Column\Integer;
use ADIOS\Core\Db\Column\Lookup;
use HubletoApp\Community\Products\Models\Product;
use HubletoApp\Community\Products\Controllers\Api\CalculatePrice;

class LeadProduct extends \HubletoMain\Core\Models\Model
{
  public string $table = 'lead_products';
  public string $recordManagerClass = RecordManagers\LeadProduct::class;
  public ?string $lookupSqlValue = '{%TABLE%}.id_product';

  public array $relations = [
    'PRODUCT' => [ self::BELONGS_TO, Product::class, 'id_product', 'id' ],
    'LEAD' => [ self::BELONGS_TO, Lead::class, 'id_lead', 'id' ],
  ];

  public function describeColumns(): array
  {
    return array_merge(parent::describeColumns(), [
      'id_lead' => (new Lookup($this, $this->translate('Lead'), Lead::class))->setRequired(),
      'id_product' => (new Lookup($this, $this->translate('Product'), Product::class))->setFkOnUpdate("CASCADE")->setFkOnDelete("SET NULL")->setRequired(),
      'unit_price' => (new Decimal($this, $this->translate('Unit Price')))->setRequired(),
      'amount' => (new Integer($this, $this->translate('Amount')))->setRequired(),
      'vat' => (new Decimal($this, $this->translate('Vat')))->setUnit("%"),
      'discount' => (new Decimal($this, $this->translate('Discount')))->setUnit("%"),
      'sum' => new Decimal($this, $this->translate('Sum')),
    ]);
  }

  public function describeTable(): \ADIOS\Core\Description\Table
  {
    $description = parent::describeTable();
    if ($this->main->urlParamAsInteger('idLead') > 0){
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
      $record["unit_price"], $record["amount"], $record["vat"] ?? 0, $record["discount"] ?? 0
    );
    return $record;
  }
  public function onBeforeUpdate(array $record): array
  {
    $record["sum"] = (new CalculatePrice($this->main))->calculatePriceIncludingVat(
      $record["unit_price"], $record["amount"], $record["vat"] ?? 0, $record["discount"] ?? 0
    );
    return $record;
  }
}
