<?php

namespace HubletoApp\Community\CalendarSync;

use HubletoApp\Community\CalendarSync\Controllers\Google;
use HubletoApp\Community\CalendarSync\Controllers\Home;
use HubletoApp\Community\CalendarSync\Controllers\Ics;

class Loader extends \Hubleto\Framework\App
{
  public const DEFAULT_INSTALLATION_CONFIG = [
    'sidebarOrder' => 0,
  ];

  /**
   * Inits the app: adds routes, settings, calendars, hooks, menu items, ...
   *
   * @return void
   * 
   */
  public function init(): void
  {
    parent::init();

    $this->getRouter()->httpGet([
      '/^settings\/calendar-sources\/google\/?$/' => Google::class,
      '/^settings\/calendar-sources\/?$/' => Home::class,
      '/^settings\/calendar-sources\/ics\/?$/' => Ics::class,
    ]);

    /** @var \HubletoApp\Community\Settings\Loader $settingsApp */
    $settingsApp = $this->getAppManager()->getApp(\HubletoApp\Community\Settings\Loader::class);
    $settingsApp->addSetting($this, [
      'title' => $this->translate('Calendar sources'),
      'icon' => 'fas fa-calendar',
      'url' => 'settings/calendar-sources',
    ]);

    $this->setConfigAsInteger('sidebarOrder', 0);

    /** @var \HubletoApp\Community\Calendar\Manager $calendarManager */
    $calendarManager = $this->getService(\HubletoApp\Community\Calendar\Manager::class);
    $calendarManager->addCalendar($this, 'sync', 'yellow', Calendar::class);
  }

  public function installTables(int $round): void
  {
    if ($round == 1) {
      $this->getModel(Models\Source::class)->dropTableIfExists()->install();
    }
  }

}
