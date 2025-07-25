<?php

namespace HubletoApp\Community\Orders\Models;

use Hubleto\Framework\Db\Column\Date;
use Hubleto\Framework\Db\Column\Decimal;
use Hubleto\Framework\Db\Column\Lookup;
use Hubleto\Framework\Db\Column\Text;
use Hubleto\Framework\Db\Column\Varchar;
use HubletoApp\Community\Customers\Models\Customer;
use HubletoApp\Community\Products\Models\Product;
use HubletoApp\Community\Settings\Models\Currency;
use HubletoApp\Community\Settings\Models\Setting;

class Order extends \Hubleto\Framework\Models\Model
{
  public string $table = 'orders';
  public string $recordManagerClass = RecordManagers\Order::class;
  public ?string $lookupSqlValue = '{%TABLE%}.order_number';

  public array $relations = [
    'PRODUCTS' => [ self::HAS_MANY, OrderProduct::class, 'id_order', 'id' ],
    'HISTORY' => [ self::HAS_MANY, History::class, 'id_order', 'id' ],
    'CUSTOMER' => [ self::HAS_ONE, Customer::class, 'id','id_customer'],
    'CURRENCY' => [ self::HAS_ONE, Currency::class, 'id', 'id_currency'],
  ];

  public function describeColumns(): array
  {
    return array_merge(parent::describeColumns(), [
      'order_number' => (new Varchar($this, $this->translate('Order number')))->setCssClass('badge badge-info'),
      'id_customer' => (new Lookup($this, $this->translate('Customer'), Customer::class))->setRequired(),
      'title' => (new Varchar($this, $this->translate('Title')))->setCssClass('font-bold'),
      'id_state' => (new Lookup($this, $this->translate('State'), State::class)),
      'price' => (new Decimal($this, $this->translate('Price')))->setReadonly()->setRequired()->setDefaultValue(0),
      'id_currency' => (new Lookup($this, $this->translate('Currency'), Currency::class))->setReadonly(),
      'date_order' => (new Date($this, $this->translate('Order date')))->setRequired()->setDefaultValue(date("Y-m-d")),
      'required_delivery_date' => (new Date($this, $this->translate('Required delivery date'))),
      'shipping_info' => (new Varchar($this, $this->translate('Shipping information'))),
      'note' => (new Text($this, $this->translate('Notes'))),
    ]);
  }

  public function describeTable(): \Hubleto\Framework\Description\Table
  {
    $description = parent::describeTable();

    $description->ui['title'] = ''; // 'Orders';
    $description->ui['addButtonText'] = $this->translate("Add order");

    unset($description->columns["shipping_info"]);
    unset($description->columns["note"]);

    return $description;
  }

  public function describeForm(): \Hubleto\Framework\Description\Form
  {
    $mSettings = $this->main->di->create(Setting::class);
    $defaultCurrency = (int) $mSettings->record
      ->where("key", "Apps\Community\Settings\Currency\DefaultCurrency")
      ->first()
      ->value
    ;

    $description = parent::describeForm();
    $description->defaultValues["id_currency"] = $defaultCurrency;

    return $description;
  }

  public function onAfterUpdate(array $originalRecord, array $savedRecord): array
  {
    $savedRecord = parent::onAfterUpdate($originalRecord, $savedRecord);

    $mProduct = $this->main->di->create(Product::class);
    $longDescription = "";

    if (isset($savedRecord["PRODUCTS"])) {
      foreach ($savedRecord["PRODUCTS"] as $product) {
        if (isset($product["_toBeDeleted_"])) {
          continue;
        }
        $productTitle = (string) $mProduct->record->find((int) $product["id_product"])->title;
        $longDescription .=  "{$productTitle} - Amount: ".(string) $product["amount"]." - Unit Price: ".(string) $product["unit_price"]." - Vat: ".(string) $product["vat"]." - Discount: ".(string) $product["discount"]." \n\n";
      }
    }

    if ($longDescription == "") {
      $longDescription = "The order had no products or all products were deleted";
    }

    $mHistory = $this->main->di->create(History::class);
    $mHistory->record->recordCreate([
      "id_order" => $savedRecord["id"],
      "short_description" => "Order has been updated",
      "long_description" => $longDescription,
      "date_time" => date("Y-m-d H:i:s"),
    ]);

    return $savedRecord;
  }

  public function onAfterCreate(array $savedRecord): array
  {
    $savedRecord = parent::onAfterCreate($savedRecord);

    $order = $this->record->find($savedRecord["id"]);
    $order->order_number = $order->id;
    $order->save();

    $mHistory = $this->main->di->create(History::class);
    $mHistory->record->recordCreate([
      "id_order" => $order->id,
      "short_description" => "Order created",
      "date_time" => date("Y-m-d H:i:s"),
    ]);

    return $savedRecord;
  }
}
