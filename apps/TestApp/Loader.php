<?php

namespace Hubleto\App\Community\TestApp;

class Loader extends \Hubleto\Erp\App
{

  /**
   * Inits the app: adds routes, settings, calendars, event listeners, menu items, ...
   *
   * @return void
   * 
   */
  public function init(): void
  {
    parent::init();

    $this->router()->get([
//      '/^customers\/api\/get-customer\/?$/' => Controllers\Api\GetCustomer::class,
    ]);

//    $this->addSearchSwitch('c', 'customers');

    /** @var \Hubleto\App\Community\Calendar\Manager $calendarManager */

    /** @var \Hubleto\App\Community\Settings\Loader $settingsApp */
//    $settingsApp = $this->appManager()->getApp(\Hubleto\App\Community\Settings\Loader::class);
//    $settingsApp->addSetting($this, [
//      'title' => $this->translate('Customer Tags'),
//      'icon' => 'fas fa-tags',
//      'url' => 'customers/tags',
//    ]);
  }

  public function installTables(int $round): void
  {

    if ($round == 1) {
      $mCustomer = $this->getModel(Models\Customer::class);

      $mCustomer->dropTableIfExists()->installTables();
    }

  }

}
