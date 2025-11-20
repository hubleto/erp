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
        'icon' => 'fas fa-envelope',
      ],
      [
        'app' => $this->app,
        'url' => 'invoices/profiles',
        'title' => $this->app->translate('Profiles'),
        'icon' => 'fas fa-box-archive',
      ],
      [
        'app' => $this->app,
        'url' => 'invoices/payments',
        'title' => $this->app->translate('Payments'),
        'icon' => 'fas fa-box-archive',
      ],
    ];
  }

}