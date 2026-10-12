<?php

namespace Hubleto\App\Community\PriceLists\Extendibles;

class Tools extends \Hubleto\Framework\Extendible
{
  public function getItems(): array
  {
    return [
      [
        'title' => $this->app->translate('Price matrix'),
        'icon' => 'fas fa-table-cells',
        'url' => 'price-lists/matrix',
      ],
      [
        'title' => $this->app->translate('Price simulator'),
        'icon' => 'fas fa-flask',
        'url' => 'price-lists/simulator',
      ],
    ];
  }
}
