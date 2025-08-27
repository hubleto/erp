<?php

namespace HubletoApp\Community\Discussions\Extendibles;

class AppMenu extends \Hubleto\Framework\Extendible
{
  public function getItems(): array
  {
    return [
      [
        'app' => $this->app,
        'url' => 'discussions',
        'title' => $this->app->translate('Discussions'),
        'icon' => 'fas fa-user',
      ],
      [
        'app' => $this->app,
        'url' => 'discussions/members',
        'title' => $this->app->translate('Members'),
        'icon' => 'fas fa-file-import',
      ],
      [
        'app' => $this->app,
        'url' => 'discussions/messages',
        'title' => $this->app->translate('Messages'),
        'icon' => 'fas fa-file-import',
      ],
    ];
  }

}