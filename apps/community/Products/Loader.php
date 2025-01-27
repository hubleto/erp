<?php

namespace HubletoApp\Community\Products;

class Loader extends \HubletoMain\Core\App
{
  public function init(): void
  {
    $this->main->router->httpGet([
      '/^products\/?$/' => Controllers\Products::class,
      '/^products\/product-groups\/?$/' => Controllers\Groups::class,
      '/^products\/suppliers\/?$/' => Controllers\Suppliers::class,
    ]);

    $this->main->sidebar->addLink(1, 99, 'products', $this->translate('Products'), 'fas fa-cart-shopping', str_starts_with($this->main->requestedUri, 'shop'));

    if (str_starts_with($this->main->requestedUri, 'products')) {
      $this->main->sidebar->addHeading1(2, 310, $this->translate('Products'));
      $this->main->sidebar->addLink(2, 320, 'products', $this->translate('Products'), 'fas fa-cart-shopping');
      $this->main->sidebar->addLink(2, 322, 'products/product-groups', $this->translate('Product Groups'), 'fas fa-burger');
      $this->main->sidebar->addLink(2, 323, 'products/suppliers', $this->translate('Suppliers'), 'fas fa-truck');
    }
  }

  public function installTables(): void
  {
    $mSupplier = new \HubletoApp\Community\Products\Models\Supplier($this->main);
    $mProduct = new \HubletoApp\Community\Products\Models\Product($this->main);
    $mProductGroup = new \HubletoApp\Community\Products\Models\Group($this->main);

    $mSupplier->dropTableIfExists()->install();
    $mProductGroup->dropTableIfExists()->install();
    $mProduct->dropTableIfExists()->install();
  }

  public function installDefaultPermissions(): void
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