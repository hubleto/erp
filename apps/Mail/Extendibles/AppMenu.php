<?php

namespace Hubleto\App\Community\Mail\Extendibles;

class AppMenu extends \Hubleto\Framework\Extendible
{
  public function getItems(): array
  {
    return [
      [
        'app' => $this->app,
        'url' => 'mail',
        'title' => $this->app->translate('Mail'),
        'icon' => 'fas fa-envelope',
      ],
      [
        'app' => $this->app,
        'url' => 'mail/templates',
        'title' => $this->app->translate('Templates'),
        'icon' => 'fas fa-box-archive',
      ],
      // [
      //   'app' => $this->app,
      //   'url' => 'mail/drafts',
      //   'title' => $this->app->translate('Drafts'),
      //   'icon' => 'fas fa-box-archive',
      // ],
      [
        'app' => $this->app,
        'url' => 'mail/accounts',
        'title' => $this->app->translate('Accounts'),
        'icon' => 'fas fa-box-archive',
      ],
    ];
  }

}