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

  public function init(): void
  {
    parent::init();

    $this->main->router->httpGet([
      '/^settings\/calendar-sources\/google\/?$/' => Google::class,
      '/^settings\/calendar-sources\/?$/' => Home::class,
      '/^settings\/calendar-sources\/ics\/?$/' => Ics::class,
    ]);

    $this->main->apps->community('Settings')?->addSetting($this, [
      'title' => $this->translate('Calendar sources'),
      'icon' => 'fas fa-calendar',
      'url' => 'settings/calendar-sources',
    ]);

    $this->setConfigAsInteger('sidebarOrder', 0);

    $calendarManager = $this->main->apps->community('Calendar')?->calendarManager?->addCalendar(
      'sync',
      'yellow',
      Calendar::class
    );
  }

  public function installTables(int $round): void
  {
    if ($round == 1) {
      $mSource = $this->main->di->create(Models\Source::class);
      $mSource->dropTableIfExists()->install();
    }
  }

}
