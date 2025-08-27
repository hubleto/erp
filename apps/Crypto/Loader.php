<?php

namespace Hubleto\App\Community\Crypto;

class Loader extends \Hubleto\Framework\App
{

  /**
   * Inits the app: adds routes, settings, calendars, hooks, menu items, ...
   *
   * @return void
   * 
   */
  public function init(): void
  {
    parent::init();

    $this->setConfigAsInteger('sidebarOrder', 0);

    $this->getRouter()->httpGet([
      '/^crypto\/?$/' => Controllers\Dashboard::class,
    ]);

    /** @var \Hubleto\App\Community\Settings\Loader $settingsApp */
    $settingsApp = $this->getAppManager()->getApp(\Hubleto\App\Community\Settings\Loader::class);
    $settingsApp->addSetting($this, [
      'title' => $this->translate('Crypto'),
      'icon' => 'fas fa-key',
      'url' => 'crypto',
    ]);
  }

}
