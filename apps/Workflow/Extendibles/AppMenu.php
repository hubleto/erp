<?php

namespace Hubleto\App\Community\Workflow\Extendibles;

class AppMenu extends \Hubleto\Framework\Extendible
{
  public function getItems(): array
  {
    return [
      [
        'app' => $this->app,
        'url' => 'workflow/history',
        'title' => $this->app->translate('History'),
        'icon' => 'fas fa-envelope',
      ],
    ];
  }

}