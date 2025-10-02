<?php

namespace Hubleto\App\Community\Warehouses\Extendibles;

class AppMenu extends \Hubleto\Framework\Extendible
{
  public function getItems(): array
  {

    return [
      [
        'app' => $this->app,
        'url' => 'warehouses',
        'title' => $this->app->translate('Warehouses'),
        'icon' => 'fas fa-warehouse',
      ],
      [
        'app' => $this->app,
        'url' => 'warehouses/locations',
        'title' => $this->app->translate('Locations'),
        'icon' => 'fas fa-pallet',
      ],
      [
        'app' => $this->app,
        'url' => 'warehouses/transactions',
        'title' => $this->app->translate('Transactions'),
        'icon' => 'fas fa-arrows-turn-to-dots',
      ],
      [
        'app' => $this->app,
        'url' => 'warehouses/transactions/add',
        'title' => $this->app->translate('Create transaction'),
        'icon' => 'fas fa-plus',
      ],
    ];
  }

}