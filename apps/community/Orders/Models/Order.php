<?php

namespace HubletoApp\Community\Orders\Models;

use HubletoApp\Community\Customers\Models\Company;
use HubletoApp\Community\Products\Models\Product;

class Order extends \HubletoMain\Core\Model
{
  public string $table = 'orders';
  public string $eloquentClass = Eloquent\Order::class;
  public ?string $lookupSqlValue = '{%TABLE%}.order_number';

  public array $relations = [
    'PRODUCTS' => [ self::HAS_MANY, OrderProduct::class, 'id_order', 'id' ],
    'HISTORY' => [ self::HAS_MANY, History::class, 'id_order', 'id' ],
    'CUSTOMER' => [ self::HAS_ONE, Company::class, 'id','id_company'],
  ];

  public function columnsLegacy(array $columns = []): array
  {
    return parent::columnsLegacy(array_merge($columns,[
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
        "model" => Company::class,
        'foreignKeyOnUpdate' => 'CASCADE',
        'foreignKeyOnDelete' => 'RESTRICT',
      ],

      "price" => [
        "type" => "float",
        "title" => $this->translate("Price"),
        "readonly" => true,
      ],

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
    $description = parent::tableDescribe();

    if (is_array($description['ui'])) {
      $description['ui']['title'] = 'Orders';
      $description["ui"]["addButtonText"] = $this->translate("Add order");
    }

    if (is_array($description['columns'])) {
      unset($description["columns"]["shipping_info"]);
      unset($description["columns"]["note"]);
    }

    return $description;
  }

  public function formDescribe(array $description = []): array
  {
    $description = parent::formDescribe();

    if (is_array($description['defaultValues'])) {
      $description["defaultValues"]["date_order"] = date("Y-m-d");
      $description["defaultValues"]["price"] = 0;
    }

    $description['includeRelations'] = [
      'PRODUCTS',
      'CUSTOMER',
      'HISTORY',
    ];

    return $description;
  }

  public function onAfterUpdate(array $originalRecord, array $savedRecord): array
  {
    $mProduct = new Product($this->main);
    $longDescription = "";

    foreach ((array) $originalRecord["PRODUCTS"] as $product) {
      if ($product["_toBeDeleted_"] == true) continue;
      $productTitle = (string) $mProduct->eloquent->find((int) $product["id_product"])->title;
      $longDescription .=  "{$productTitle} - Amount: ".(string) $product["amount"]." - Unit Price: ".(string) $product["unit_price"]." - Tax: ".(string) $product["tax"]." - Discount: ".(string) $product["discount"]." \n\n";
    }

    if ($longDescription == "") $longDescription = "The order had no products or all products were deleted";

    $mHistory = new History($this->main);
    $mHistory->eloquent->create([
      "id_order" => $originalRecord["id"],
      "short_description" => "Order has been updated",
      "long_description" => $longDescription,
      "date_time" => date("Y-m-d H:i:s"),
    ]);

    return parent::onAfterUpdate($originalRecord, $savedRecord);
  }

  public function onAfterCreate(array $originalRecord, array $savedRecord): array
  {

    $order = $this->eloquent->find($savedRecord["id"]);
    $order->order_number = $order->id;
    $order->save();

    $mHistory = new History($this->main);
    $mHistory->eloquent->create([
      "id_order" => $order->id,
      "short_description" => "Order created",
      "date_time" => date("Y-m-d H:i:s"),
    ]);

    return parent::onAfterCreate($originalRecord, $savedRecord);
  }
}
