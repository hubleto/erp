<?php

namespace HubletoApp\Community\Products\Models;

class Product extends \HubletoMain\Core\Model
{
  public string $table = 'products';
  public string $eloquentClass = Eloquent\Product::class;
  public ?string $lookupSqlValue = '{%TABLE%}.title';

  public array $relations = [
    'GROUP' => [ self::HAS_ONE, Group::class, 'id','id_product_group'],
    'SUPPLIER' => [ self::HAS_ONE, Supplier::class, 'id','id_supplier'],
  ];

  public function columns(array $columns = []): array
  {
    return parent::columns(array_merge($columns,[

      "title" => [
        "type" => "varchar",
        "title" => $this->translate("Title"),
        "required" => true,
      ],

      "id_product_group" => [
        "type" => "lookup",
        "model" => Group::class,
        "title" => $this->translate("Group"),
        "required" => false,
        'foreignKeyOnUpdate' => 'CASCADE',
        'foreignKeyOnDelete' => 'SET NULL',
      ],

      "id_supplier" => [
        "type" => "lookup",
        "model" => Supplier::class,
        "title" => $this->translate("Supplier"),
        "required" => false,
        'foreignKeyOnUpdate' => 'CASCADE',
        'foreignKeyOnDelete' => 'SET NULL',
      ],

      "is_on_sale" => [
        "type" => "boolean",
        "title" => $this->translate("On sale"),
        "required" => false,
      ],

      "image" => [
        'type' => 'image',
        'title' => $this->translate("Image").' [540x600px]',
        'required' => false,
      ],

      "description" => [
        "type" => "text",
        "title" => $this->translate("Description"),
        "required" => false,
      ],

      "count_in_package" => [
        "type" => "float",
        "title" => $this->translate("Number of items in package"),
        "required" => false,
      ],

      "unit_price" => [
        "type" => "float",
        "title" => $this->translate("Single unit price"),
        "required" => true,
      ],

      "margin" => [
        "type" => "float",
        "title" => $this->translate("Margin"),
        "unit" => "%",
        "required" => false,
      ],

      "tax" => [
        "type" => "float",
        "title" => $this->translate("Tax"),
        "unit" => "%",
        "required" => true,
      ],

      "is_single_order_posible" => [
        "type" => "boolean",
        "title" => $this->translate("Single unit order posible"),
        "required" => false,
      ],

      "unit" => [
        "type" => "varchar",
        "title" => $this->translate("Unit"),
        "required" => false,
      ],

      "packaging" => [
        "type" => "varchar",
        "title" => $this->translate("Packaging"),
        "required" => false,
      ],

      /* "netto_obsah" => [
        "type" => "float",
        "title" => "Netto obsah",
        "required" => TRUE,
      ], */

      /* "netto_jednotka" => [
        "type" => "varchar",
        "title" => "Netto jednotka",
        "required" => TRUE,
      ], */

      "sale_ended" => [
        "type" => "date",
        "title" => $this->translate("Sale ended"),
        "required" => false,
      ],

      "show_price" => [
        "type" => "boolean",
        "title" => $this->translate("Show price to customer"),
        "required" => false,
      ],

      "price_after_reweight" => [
        "type" => "boolean",
        "title" => $this->translate("Set price after reweight?"),
        "required" => false,
      ],

      "needs_reodering" => [
        "type" => "boolean",
        "title" => $this->translate("Needs reordering?"),
        "required" => false,
      ],

      "storage_rules" => [
        "type" => "text",
        "title" => $this->translate("Storage rules"),
        "required" => false,
      ],

      "table" => [
        "type" => "text",
        "title" => $this->translate("Table"),
        "required" => false,
      ],

    ]));
  }

  public function tableDescribe(array $description = []): array
  {
    $description["model"] = $this->fullName;
    $description = parent::tableDescribe();

    $description['ui']['title'] = 'Products';
    $description["ui"]["addButtonText"] = $this->translate("Add product");

    unset($description["columns"]["is_on_sale"]);
    unset($description["columns"]["image"]);
    unset($description["columns"]["count_in_package"]);
    unset($description["columns"]["is_single_order_posible"]);
    unset($description["columns"]["packaging"]);
    unset($description["columns"]["show_price"]);
    unset($description["columns"]["price_after_reweight"]);
    unset($description["columns"]["needs_reodering"]);
    unset($description["columns"]["storage_rules"]);
    unset($description["columns"]["table"]);
    unset($description["columns"]["description"]);

    return $description;
  }
}
