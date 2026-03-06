<?php

namespace Hubleto\App\Community\Orders\Extendibles;

class AppMenu extends \Hubleto\Framework\Extendible
{
  public function getItems(): array
  {
    return [
      [
        'app' => $this->app,
        'url' => 'orders',
        'title' => $this->app->translate('Orders'),
        'icon' => 'fas fa-money-check-dollar',
      ],
      [
        'app' => $this->app,
        'url' => 'orders/items',
        'title' => $this->app->translate('Items'),
        'icon' => 'fas fa-list',
      ],
      [
        'app' => $this->app,
        'url' => 'orders/quotes',
        'title' => $this->app->translate('Quotes'),
        'icon' => 'fas fa-receipt',
      ],
    ];
  }

}