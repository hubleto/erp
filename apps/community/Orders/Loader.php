<?php

namespace HubletoApp\Community\Orders;

class Loader extends \HubletoMain\Core\App
{
  public function init(): void
  {
    $this->main->router->httpGet([
      '/^orders\/?$/' => Controllers\Orders::class,
    ]);

    $this->main->sidebar->addLink(1, 100, 'orders', $this->translate('Orders'), 'fas fa-file-lines', str_starts_with($this->main->requestedUri, 'shop'));
  }

  public function installTables() {
    $mOrder = new \HubletoApp\Community\Orders\Models\Order($this->main);
    $mOrderProduct = new \HubletoApp\Community\Orders\Models\OrderProduct($this->main);
    $mHistory = new \HubletoApp\Community\Orders\Models\History($this->main);

    $mOrder->dropTableIfExists()->install();
    $mOrderProduct->dropTableIfExists()->install();
    $mHistory->dropTableIfExists()->install();
  }

  public function installDefaultPermissions()
  {
    $mPermission = new \HubletoApp\Community\Settings\Models\Permission($this->main);
    $permissions = [];

    foreach ($permissions as $key => $permission) {
      $mPermission->eloquent->create([
        "permission" => $permission
      ]);
    }
  }
}