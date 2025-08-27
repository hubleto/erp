<?php

namespace Hubleto\App\Community\Products;

class Loader extends \Hubleto\Framework\App
{

  public array $productTypes = [];

  /**
   * Inits the app: adds routes, settings, calendars, hooks, menu items, ...
   *
   * @return void
   * 
   */
  public function init(): void
  {
    parent::init();

    $this->getRouter()->httpGet([
      '/^products\/?$/' => Controllers\Products::class,
      '/^products\/groups\/?$/' => Controllers\Groups::class,
      '/^products\/groups(\/(?<recordId>\d+))?\/?$/' => Controllers\Groups::class,
    ]);

    $appMenu = $this->getService(\Hubleto\App\Community\Desktop\AppMenuManager::class);
    $appMenu->addItem($this, 'products', $this->translate('Products'), 'fas fa-cart-shopping');
    $appMenu->addItem($this, 'products/groups', $this->translate('Groups'), 'fas fa-burger');

    $this->productTypes = $this->collectExtendibles('ProductTypes');
  }

  public function installTables(int $round): void
  {
    if ($round == 1) {
      $this->getModel(Models\Group::class)->dropTableIfExists()->install();
      $this->getModel(Models\Product::class)->dropTableIfExists()->install();
      $this->getModel(Models\ProductSupplier::class)->dropTableIfExists()->install();
    }
  }

}
