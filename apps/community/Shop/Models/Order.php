<?php

namespace HubletoApp\Community\Shop\Models;

use HubletoApp\Community\Customers\Models\Company;

class Order extends \HubletoMain\Core\Model
{
  public string $table = 'orders';
  public string $eloquentClass = Eloquent\Order::class;
  public ?string $lookupSqlValue = '{%TABLE%}.order_number';

  public array $relations = [
    'PRODUCTS' => [ self::HAS_MANY, OrderProduct::class, 'id_order', 'id' ],
    'CUSTOMER' => [ self::HAS_ONE, Company::class, 'id','id_company'],
  ];

  public function columns(array $columns = []): array
  {
    return parent::columns(array_merge($columns,[
      "order_number" => [
        "type" => "int",
        "title" => $this->translate("Order Number"),
        "required" => false,
        "readonly" => true,
      ],

      "id_company" => [
        "type" => "lookup",
        "title" => $this->translate("Customer"),
        "required" => true,
        "model" => Company::class
      ],

      "price" => [
        "type" => "float",
        "title" => $this->translate("Price"),
        //"required" => true,
        "readonly" => true,
      ],

      /* "id_klient" => [
        "type" => "lookup",
        "title" => "Klient",
        "model" => "Widgets/Klienti/Models/Klient",
        "show_column" => TRUE,
      ], */

      "date_order" => [
        "type" => "date",
        "title" => $this->translate("Order Date"),
        "required" => true
      ],

      "required_delivery_date" => [
        "type" => "date",
        "title" => $this->translate("Required Delivery date"),
        "required" => false
      ],

      "shipping_info" => [
        "type" => "varchar",
        "title" =>  $this->translate("Shipping information"),
        "required" => false,
      ],

      /* "zakaznicke_objednavacie_cislo" => [
        "type" => "varchar",
        "title" => "Zákaznícke objednávacie číslo",
        "show_column" => TRUE,
      ], */

      "note" => [
        "type" => "text",
        "title" =>  $this->translate("Notes"),
        "required" => false,
      ],

      /* "status" => [
        "type" => "lookup",
        "enum_values" => $this->enum_objednavky_stavy,
        "title" => "Stav",
        "show_column" => true
      ], */

      /* "id_faktura" => [
        "type" => "lookup",
        "title" => "Faktúra",
        "model" => "Widgets/Faktury/Models/Faktura",
        "show_column" => TRUE,
      ], */

    ]));
  }

  public function tableDescribe(array $description = []): array
  {
    $description["model"] = $this->fullName;
    $description = parent::tableDescribe();

    $description['ui']['title'] = 'Orders';
    $description["ui"]["addButtonText"] = $this->translate("Add order");

    unset($description["columns"]["shipping_info"]);
    unset($description["columns"]["note"]);

    return $description;
  }

  public function formDescribe(array $description = []): array
  {
    $description = parent::formDescribe();
    $description["defaultValues"]["date_order"] = date("Y-m-d");
    $description["defaultValues"]["price"] = 0;
    ;
    $description['includeRelations'] = [
      'PRODUCTS',
      'CUSTOMER',
    ];

    return $description;
  }

  public function onAfterCreate(array $record, $returnValue)
  {

    $order = $this->eloquent->find($record["id"]);
    $order->order_number = $order->id;
    $order->save();

    return parent::onAfterCreate($record, $returnValue);
  }
}
