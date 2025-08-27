<?php

namespace HubletoApp\Community\Contacts\Extendibles;

class AppMenu extends \Hubleto\Framework\Extendible
{
  public function getItems(): array
  {
    return [
      [
        'app' => $this->app,
        'url' => 'contacts',
        'title' => $this->app->translate('Contacts'),
        'icon' => 'fas fa-user',
      ],
      [
        'app' => $this->app,
        'url' => 'contacts/import',
        'title' => $this->app->translate('Import contacts'),
        'icon' => 'fas fa-file-import',
      ],
    ];
  }

}