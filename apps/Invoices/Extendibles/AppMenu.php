<?php

namespace Hubleto\App\Community\Invoices\Extendibles;

class AppMenu extends \Hubleto\Framework\Extendible
{
  public function getItems(): array
  {
    return [
      [
        'app' => $this->app,
        'url' => 'invoices',
        'title' => $this->app->translate('Invoices'),
        'icon' => 'fas fa-file-invoice',
      ],
      [
        'app' => $this->app,
        'url' => 'invoices/items',
        'title' => $this->app->translate('items'),
        'icon' => 'fas fa-list',
      ],
      [
        'app' => $this->app,
        'url' => 'invoices/payments',
        'title' => $this->app->translate('Payments'),
        'icon' => 'fas fa-euro-sign',
      ],
      [
        'app' => $this->app,
        'url' => 'invoices/payment-methods',
        'title' => $this->app->translate('Payment methods'),
        'icon' => 'fas fa-wallet',
      ],
      [
        'app' => $this->app,
        'url' => 'invoices/profiles',
        'title' => $this->app->translate('Profiles'),
        'icon' => 'fas fa-address-card',
      ],
    ];
  }

}