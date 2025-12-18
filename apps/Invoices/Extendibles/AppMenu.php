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
        'url' => 'invoices/items?filters[fStatus]=1',
        'title' => $this->app->translate('Prepared items'),
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
        'url' => 'invoices/profiles',
        'title' => $this->app->translate('Profiles'),
        'icon' => 'fas fa-address-card',
      ],
    ];
  }

}