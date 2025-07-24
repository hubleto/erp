<?php

namespace HubletoApp\Community\Orders;

class Loader extends \Hubleto\Framework\App
{
  public function init(): void
  {
    parent::init();

    $this->main->router->httpGet([
      '/^orders\/?$/' => Controllers\Orders::class,
      '/^settings\/order-states\/?$/' => Controllers\States::class,
    ]);

    $this->main->apps->community('Settings')->addSetting($this, [
      'title' => $this->translate('Order states'),
      'icon' => 'fas fa-file-lines',
      'url' => 'settings/order-states',
    ]);
  }

  public function installTables(int $round): void
  {
    if ($round == 1) {
      (new Models\State($this->main))->dropTableIfExists()->install();
      (new Models\Order($this->main))->dropTableIfExists()->install();
      (new Models\OrderProduct($this->main))->dropTableIfExists()->install();
      (new Models\History($this->main))->dropTableIfExists()->install();
    }

    if ($round == 2) {
      $mState = $this->main->di->create(Models\State::class);
      $mState->record->recordCreate(['title' => 'New', 'code' => 'N', 'color' => '#444444']);
      $mState->record->recordCreate(['title' => 'Sent to customer', 'code' => 'S', 'color' => '#444444']);
      $mState->record->recordCreate(['title' => 'Requires modification', 'code' => 'M', 'color' => '#444444']);
      $mState->record->recordCreate(['title' => 'Accepted', 'code' => 'A', 'color' => '#444444']);
      $mState->record->recordCreate(['title' => 'Modified', 'code' => 'M', 'color' => '#444444']);
      $mState->record->recordCreate(['title' => 'Order created', 'code' => 'O', 'color' => '#444444']);
      $mState->record->recordCreate(['title' => 'Rejected', 'code' => 'R', 'color' => '#444444']);
    }
  }

  // public function installDefaultPermissions(): void
  // {
  //   $mPermission = $this->main->di->create(\HubletoApp\Community\Settings\Models\Permission::class);
  //   $permissions = [
  //     "HubletoApp/Community/Orders/Models/Order:Create",
  //     "HubletoApp/Community/Orders/Models/Order:Read",
  //     "HubletoApp/Community/Orders/Models/Order:Update",
  //     "HubletoApp/Community/Orders/Models/Order:Delete",

  //     "HubletoApp/Community/Orders/Models/History:Create",
  //     "HubletoApp/Community/Orders/Models/History:Read",
  //     "HubletoApp/Community/Orders/Models/History:Update",
  //     "HubletoApp/Community/Orders/Models/History:Delete",

  //     "HubletoApp/Community/Orders/Models/OrderProduct:Create",
  //     "HubletoApp/Community/Orders/Models/OrderProduct:Read",
  //     "HubletoApp/Community/Orders/Models/OrderProduct:Update",
  //     "HubletoApp/Community/Orders/Models/OrderProduct:Delete",

  //     "HubletoApp/Community/Orders/Controllers/Orders",

  //     "HubletoApp/Community/Orders/Orders",
  //   ];

  //   foreach ($permissions as $permission) {
  //     $mPermission->record->recordCreate([
  //       "permission" => $permission
  //     ]);
  //   }
  // }

  public function generateDemoData(): void
  {
    $mUser = $this->main->di->create(\HubletoApp\Community\Settings\Models\User::class);
    $userCount = $mUser->record->count();

    $mCustomer = $this->main->di->create(\HubletoApp\Community\Customers\Models\Customer::class);
    $customerCount = $mCustomer->record->count();

    $mState = $this->main->di->create(Models\State::class);
    $stateCount = $mState->record->count();

    $mOrder = $this->main->di->create(Models\Order::class);
    $mHistory = $this->main->di->create(Models\History::class);
    $mOrderProduct = $this->main->di->create(Models\OrderProduct::class);

    for ($i = 1; $i <= 9; $i++) {

      $idOrder = $mOrder->record->recordCreate([
        'id_customer' => rand(1, $customerCount),
        'id_state' => rand(1, $stateCount),
        'order_number' => 'O' . date('Y') . '-00' . $i,
        'title' => 'This is a test bid #' . $i,
        'price' => rand(1000, 2000) / rand(3, 5),
        'id_currency' => 1,
        'date_order' => date('Y-m-d', strtotime('-' . rand(0, 10) . ' days')),
      ])['id'];

      $mHistory->record->recordCreate([ 'id_order' => $idOrder, 'short_description' => 'Order created', 'date_time' => date('Y-m-d H:i:s') ]);

      for ($j = 1; $j <= 5; $j++) {
        $amount = rand(100, 200) / rand(3, 7);
        $unitPrice = rand(50, 80) / rand(2, 5);
        $mOrderProduct->record->recordCreate([
          'id_order' => $idOrder,
          'title' => 'Item #' . $i . '.' . $j,
          'amount' => $amount,
          'unit_price' => $unitPrice,
        ]);
      }
    }

  }

}
