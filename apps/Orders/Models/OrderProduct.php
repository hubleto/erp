<?php

namespace HubletoApp\Community\Orders\Models;

use Hubleto\Framework\Db\Column\Varchar;
use Hubleto\Framework\Db\Column\Decimal;
use Hubleto\Framework\Db\Column\Integer;
use Hubleto\Framework\Db\Column\Lookup;
use HubletoApp\Community\Products\Models\Product;

class OrderProduct extends \Hubleto\Framework\Models\Model
{
  public string $table = 'order_products';
  public string $recordManagerClass = RecordManagers\OrderProduct::class;
  public ?string $lookupSqlValue = '{%TABLE%}.id';

  public array $relations = [
    'ORDER'   => [ self::BELONGS_TO, Order::class, 'id_order', 'id'],
    'PRODUCT' => [ self::BELONGS_TO, Product::class, 'id_product', 'id'],
  ];

  public function describeColumns(): array
  {
    return array_merge(parent::describeColumns(), [
      'id_order' => (new Lookup($this, $this->translate('Order'), Order::class))->setRequired(),
      'title' => (new Varchar($this, $this->translate('Title')))->setRequired(),
      'id_product' => (new Lookup($this, $this->translate('Product'), Product::class)),
      'unit_price' => (new Decimal($this, $this->translate('Unit price')))->setRequired(),
      'amount' => (new Integer($this, $this->translate('Amount')))->setRequired(),
      'discount' => (new Integer($this, $this->translate('Discount')))->setUnit('%'),
      'vat' => (new Integer($this, $this->translate('Vat')))->setUnit('%')->setRequired(),
    ]);
  }

  public function describeTable(): \Hubleto\Framework\Description\Table
  {
    $description = parent::describeTable();

    $description->ui['title'] = 'Order Products';
    $description->ui["addButtonText"] = $this->translate("Add product");

    if ($this->main->urlParamAsInteger('idOrder') > 0) {
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
}
