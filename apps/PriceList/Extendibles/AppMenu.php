<?php

namespace Hubleto\App\Community\PriceList\Extendibles;

class AppMenu extends \Hubleto\Framework\Extendible
{
  public function getItems(): array
  {
    return [
      [
        'app' => $this->app,
        'url' => 'price-lists',
        'title' => $this->app->translate('Price lists'),
        'icon' => 'fas fa-tags',
      ],
      [
        'app' => $this->app,
        'url' => 'price-lists/items',
        'title' => $this->app->translate('Prices'),
        'icon' => 'fas fa-list',
      ],
      [
        'app' => $this->app,
        'url' => 'price-lists/vat-rates',
        'title' => $this->app->translate('VAT rates'),
        'icon' => 'fas fa-receipt',
      ],
      [
        'app' => $this->app,
        'url' => 'price-lists/simulator',
        'title' => $this->app->translate('Price simulator'),
        'icon' => 'fas fa-flask',
      ],
    ];
  }
}
