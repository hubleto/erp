<?php

namespace Hubleto\App\Community\CalendarSync;

use Hubleto\App\Community\CalendarSync\Controllers\Google;
use Hubleto\App\Community\CalendarSync\Controllers\Home;
use Hubleto\App\Community\CalendarSync\Controllers\Ics;

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

    $this->setConfigAsInteger('sidebarOrder', 0);

    $this->router()->get([
      '/^settings\/calendar-sources\/google\/?$/' => Google::class,
      '/^settings\/calendar-sources\/?$/' => Home::class,
      '/^settings\/calendar-sources\/ics\/?$/' => Ics::class,
    ]);

    /** @var \Hubleto\App\Community\Settings\Loader $settingsApp */
    $settingsApp = $this->appManager()->getApp(\Hubleto\App\Community\Settings\Loader::class);
    $settingsApp->addSetting($this, [
      'title' => $this->translate('Calendar sources'),
      'icon' => 'fas fa-calendar',
      'url' => 'settings/calendar-sources',
    ]);

    /** @var \Hubleto\App\Community\Calendar\Manager $calendarManager */
    $calendarManager = $this->getService(\Hubleto\App\Community\Calendar\Manager::class);
    $calendarManager->addCalendar($this, 'sync', 'yellow', Calendar::class);
  }

  public function installTables(int $round): void
  {
    if ($round == 1) {
      $this->getModel(Models\Source::class)->dropTableIfExists()->install();
    }
  }

}
