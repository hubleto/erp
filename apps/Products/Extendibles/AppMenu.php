<?php

namespace Hubleto\App\Community\Products\Extendibles;

class AppMenu extends \Hubleto\Framework\Extendible
{
  public function getItems(): array
  {
    return [
      [
        'app' => $this->app,
        'url' => 'products',
        'title' => $this->app->translate('Products'),
        'icon' => 'fas fa-cart-shopping',
      ],
      [
        'app' => $this->app,
        'url' => 'products/categories',
        'title' => $this->app->translate('Categories'),
        'icon' => 'fas fa-folder-tree',
      ],
      [
        'app' => $this->app,
        'url' => 'products/groups',
        'title' => $this->app->translate('Groups'),
        'icon' => 'fas fa-layer-group',
      ],
    ];
  }

}