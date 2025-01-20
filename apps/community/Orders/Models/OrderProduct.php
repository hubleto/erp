<?php

namespace HubletoApp\Community\Orders\Models;

use HubletoApp\Community\Products\Models\Product;

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
      "id_product" => [
        "type" => "lookup",
        "model" => Product::class,
        "title" => $this->translate("Product"),
        "required" => true,
        'foreignKeyOnUpdate' => 'CASCADE',
        'foreignKeyOnDelete' => 'RESTRICT',
      ],

      "id_order" => [
        "type" => "lookup",
        "model" => Order::class,
        "title" => $this->translate("Order"),
        "required" => true,
        'foreignKeyOnUpdate' => 'CASCADE',
        'foreignKeyOnDelete' => 'CASCADE',
      ],

      "unit_price" => [
        "type" => "float",
        "title" => $this->translate("Unit Price"),
        "required" => true,
      ],

      "amount" => [
        "type" => "int",
        "title" => $this->translate("Amount"),
        "required" => true,
      ],

      "discount" => [
        "type" => "int",
        "title" => $this->translate("Dicount (%)"),
        "required" => false,
      ],

      "tax" => [
        "type" => "int",
        "title" => $this->translate("Tax (%)"),
        "required" => true,
      ],

    ]));
  }

  public function tableDescribe(array $description = []): array
  {
    $description["model"] = $this->fullName;
    $description = parent::tableDescribe();

    $description['ui']['title'] = 'Order Products';
    $description["ui"]["addButtonText"] = $this->translate("Add product");

    return $description;
  }
}
