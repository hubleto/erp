<?php

namespace Hubleto\App\Community\EmailMarketing\Extendibles;

class AppMenu extends \Hubleto\Framework\Extendible
{
  public function getItems(): array
  {
    return [
      [
        'app' => $this->app,
        'url' => 'email-marketing/emails',
        'title' => $this->app->translate('Emails'),
        'icon' => 'fas fa-users-viewfinder',
      ],
      [
        'app' => $this->app,
        'url' => 'email-marketing/email-recipients',
        'title' => $this->app->translate('Recipients'),
        'icon' => 'fas fa-paper-plane',
      ],
      [
        'app' => $this->app,
        'url' => 'email-marketing/recipient-statuses',
        'title' => $this->app->translate('Recipient statuses'),
        'icon' => 'fas fa-check-double',
      ],
    ];
  }

}