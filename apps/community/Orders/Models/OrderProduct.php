<?php

namespace HubletoApp\Community\Orders\Models;

use HubletoApp\Community\Products\Models\Product;

use \ADIOS\Core\Db\Column\Lookup;
use \ADIOS\Core\Db\Column\Decimal;
use \ADIOS\Core\Db\Column\Integer;

class OrderProduct extends \HubletoMain\Core\Model
{
  public string $table = 'order_products';
  public string $eloquentClass = Eloquent\OrderProduct::class;
  public ?string $lookupSqlValue = '{%TABLE%}.id';

  public array $relations = [
    'ORDER'   => [ self::BELONGS_TO, Order::class, 'id_order', 'id'],
    'PRODUCT' => [ self::BELONGS_TO, Product::class, 'id_product', 'id'],
  ];

  public function columns(array $columns = []): array
  {
    return parent::columns(array_merge($columns,[
      'id_product' => (new Lookup($this, $this->translate('Product'), Product::class))->setRequired()->setFkOnUpdate('CASCADE')->setFkOnDelete('RESTRICT'),
      'id_order' => (new Lookup($this, $this->translate('Order'), Order::class))->setRequired(),
      'unit_price' => (new Decimal($this, $this->translate('Unit price')))->setRequired(),
      'amount' => (new Integer($this, $this->translate('Amount')))->setRequired(),
      'discount' => (new Integer($this, $this->translate('Discount (%)'))),
      'tax' => (new Integer($this, $this->translate('Tax (%)')))->setRequired(),
    ]));
  }

  public function tableDescribe(): \ADIOS\Core\Description\Table
  {
    $description = parent::tableDescribe();

    $description->ui['title'] = 'Order Products';
    $description->ui["addButtonText"] = $this->translate("Add product");

    if ($this->main->urlParamAsBool('idOrder') > 0) {
      $description->permissions = [
        'canRead' => $this->app->permissions->granted($this->fullName . ':Read'),
        'canCreate' => $this->app->permissions->granted($this->fullName . ':Create'),
        'canUpdate' => $this->app->permissions->granted($this->fullName . ':Update'),
        'canDelete' => $this->app->permissions->granted($this->fullName . ':Delete'),
      ];
    }

    return $description;
  }
}
