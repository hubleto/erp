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
        'title' => $this->app->translate('All orders'),
        'icon' => 'fas fa-money-check-dollar',
      ],
      [
        'app' => $this->app,
        'url' => 'orders?view=purchaseOrders',
        'title' => $this->app->translate('Purchase orders'),
        'icon' => 'fas fa-cart-shopping',
      ],
      [
        'app' => $this->app,
        'url' => 'orders?view=salesOrders',
        'title' => $this->app->translate('Sales orders'),
        'icon' => 'fas fa-euro-sign',
      ],
      [
        'app' => $this->app,
        'url' => 'orders/payments',
        'title' => $this->app->translate('Payments'),
        'icon' => 'fas fa-euro-sign',
      ],
    ];
  }

}