<?php

namespace HubletoApp\Community\Products;

class Loader extends \Hubleto\Framework\App
{
  public function init(): void
  {
    parent::init();

    $this->main->router->httpGet([
      '/^products\/?$/' => Controllers\Products::class,
      '/^products\/groups\/?$/' => Controllers\Groups::class,
      '/^products\/groups(\/(?<recordId>\d+))?\/?$/' => Controllers\Groups::class,
    ]);

    $appMenu = $this->main->apps->community('Desktop')->appMenu;
    $appMenu->addItem($this, 'products', $this->translate('Products'), 'fas fa-cart-shopping');
    $appMenu->addItem($this, 'products/groups', $this->translate('Groups'), 'fas fa-burger');
  }

  public function installTables(int $round): void
  {
    if ($round == 1) {
      $mProduct = $this->main->di->create(\HubletoApp\Community\Products\Models\Product::class);
      $mProductGroup = $this->main->di->create(\HubletoApp\Community\Products\Models\Group::class);

      $mProductGroup->dropTableIfExists()->install();
      $mProduct->dropTableIfExists()->install();
    }
  }

}
