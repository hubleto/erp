<?php

namespace HubletoApp\Community\Orders\Models;

use HubletoApp\Community\Customers\Models\Company;
use HubletoApp\Community\Products\Models\Product;

use \ADIOS\Core\Db\Column\Lookup;
use \ADIOS\Core\Db\Column\Integer;
use \ADIOS\Core\Db\Column\Decimal;
use \ADIOS\Core\Db\Column\Date;
use \ADIOS\Core\Db\Column\Varchar;
use \ADIOS\Core\Db\Column\Text;

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

  public function columns(array $columns = []): array
  {
    return parent::columns(array_merge($columns,[
      'order_number' => (new Varchar($this, $this->translate('Order number')))->setReadonly(),
      'id_company' => (new Lookup($this, $this->translate('Customer'), Company::class))->setFkOnUpdate('CASCADE')->setFkOnDelete('RESTRICT')->setRequired(),
      'price' => (new Decimal($this, $this->translate('Price')))->setReadonly(),
      'date_order' => (new Date($this, $this->translate('Order date')))->setRequired(),
      'required_delivery_date' => (new Date($this, $this->translate('Required delivery date')))->setRequired(),
      'shipping_info' => (new Varchar($this, $this->translate('Shipping information'))),
      'note' => (new Text($this, $this->translate('Notes'))),

      /* "zakaznicke_objednavacie_cislo" => [
        "type" => "varchar",
        "title" => "Zákaznícke objednávacie číslo",
        "show_column" => TRUE,
      ], */

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

  public function describeTable(): \ADIOS\Core\Description\Table
  {
    $description = parent::describeTable();

    $description->ui['title'] = 'Orders';
    $description->ui['addButtonText'] = $this->translate("Add order");

    unset($description->columns["shipping_info"]);
    unset($description->columns["note"]);

    return $description;
  }

  public function describeForm(): \ADIOS\Core\Description\Form
  {
    $description = parent::describeForm();
    $description->defaultValues["date_order"] = date("Y-m-d");
    $description->defaultValues["price"] = 0;
    return $description;
  }

  public function onAfterUpdate(array $originalRecord, array $savedRecord): array
  {
    $mProduct = new Product($this->main);
    $longDescription = "";

    foreach ((array) $originalRecord["PRODUCTS"] as $product) {
      if (isset($product["_toBeDeleted_"])) continue;
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
