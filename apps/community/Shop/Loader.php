<?php

namespace HubletoApp\Community\Shop;

class Loader extends \HubletoMain\Core\App
{
  public function init(): void
  {
    $this->main->router->httpGet([
      '/^shop\/products\/?$/' => Controllers\Products::class,
      '/^shop\/product-groups\/?$/' => Controllers\ProductGroups::class,
      '/^shop\/orders\/?$/' => Controllers\Orders::class,
      '/^shop\/suppliers\/?$/' => Controllers\ProductSuppliers::class,
    ]);

    $this->main->sidebar->addLink(1, 99, 'shop/orders', $this->translate('Shop'), 'fas fa-shop', str_starts_with($this->main->requestedUri, 'shop'));

    if (str_starts_with($this->main->requestedUri, 'shop')) {
      $this->main->sidebar->addHeading1(2, 310, $this->translate('Shop'));
      $this->main->sidebar->addLink(2, 320, 'shop/products', $this->translate('Products'), 'fas fa-cart-shopping');
      $this->main->sidebar->addLink(2, 321, 'shop/orders', $this->translate('Orders'), 'fas fa-file-lines');
      $this->main->sidebar->addLink(2, 322, 'shop/product-groups', $this->translate('Product Groups'), 'fas fa-burger');
      $this->main->sidebar->addLink(2, 323, 'shop/suppliers', $this->translate('Suppliers'), 'fas fa-truck');
    }
  }

  public function installTables() {
    $mSupplier = new \HubletoApp\Community\Shop\Models\ProductSupplier($this->main);
    $mProduct = new \HubletoApp\Community\Shop\Models\Product($this->main);
    $mProductGroup = new \HubletoApp\Community\Shop\Models\ProductGroup($this->main);
    $mOrder = new \HubletoApp\Community\Shop\Models\Order($this->main);
    $mOrderProduct = new \HubletoApp\Community\Shop\Models\OrderProduct($this->main);

    $mSupplier->dropTableIfExists()->install();
    $mProductGroup->dropTableIfExists()->install();
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