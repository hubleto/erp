<?php

namespace Hubleto\App\Community\Cashdesk\Extendibles;

class AppMenu extends \Hubleto\Framework\Extendible
{
  public function getItems(): array
  {
    return [
      [
        'app' => $this->app,
        'url' => 'cashdesk',
        'title' => $this->app->translate('Cashdesk'),
        'icon' => 'fas fa-cash-register',
      ],
      [
        'app' => $this->app,
        'url' => 'cashdesk/cash-registers',
        'title' => $this->app->translate('Cash registers'),
        'icon' => 'fas fa-cash-register',
      ],
      [
        'app' => $this->app,
        'url' => 'cashdesk/receipts',
        'title' => $this->app->translate('Receipts'),
        'icon' => 'fas fa-receipt',
      ],
    ];
  }

}