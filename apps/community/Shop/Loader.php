<?php

namespace HubletoApp\Community\Shop;

class Loader extends \HubletoMain\Core\App
{
  public function init(): void
  {
    $this->main->router->httpGet([
      '/^shop\/products\/?$/' => Controllers\Products::class,
      '/^shop\/orders\/?$/' => Controllers\Orders::class,
    ]);

    $this->main->sidebar->addLink(1, 99, 'shop/products', $this->translate('Shop'), 'fas fa-shop', str_starts_with($this->main->requestedUri, 'shop'));

    if (str_starts_with($this->main->requestedUri, 'shop')) {
      $this->main->sidebar->addHeading1(2, 310, $this->translate('Shop'));
      $this->main->sidebar->addLink(2, 320, 'shop/products', $this->translate('Products'), 'fas fa-cart-shopping');
      $this->main->sidebar->addLink(2, 330, 'shop/orders', $this->translate('Orders'), 'fas fa-file-lines');
    }
  }

  public function installTables() {
    $mProduct = new \HubletoApp\Community\Shop\Models\Product($this->main);
    $mOrder = new \HubletoApp\Community\Shop\Models\Order($this->main);
    $mOrderProduct = new \HubletoApp\Community\Shop\Models\OrderProduct($this->main);

    $mProduct->dropTableIfExists()->install();
    $mOrder->dropTableIfExists()->install();
    $mOrderProduct->dropTableIfExists()->install();
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