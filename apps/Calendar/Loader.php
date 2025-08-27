<?php

namespace HubletoApp\Community\Calendar;

use HubletoApp\Community\Calendar\Models\Activity;
use HubletoApp\Community\Calendar\Models\SharedCalendar;

class Loader extends \Hubleto\Framework\App
{
  public bool $hasCustomSettings = true;

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
      '/^calendar\/?$/' => Controllers\Calendar::class,
      '/^calendar(\/(?<key>\w+))?\/ics\/?$/' => Controllers\IcsCalendar::class,
      '/^calendar\/settings\/?$/' => Controllers\Settings::class,
      '/^calendar\/boards\/reminders\/?$/' => Controllers\Boards\Reminders::class,
      '/^calendar\/api\/get-calendar-events\/?$/' => Controllers\Api\GetCalendarEvents::class,
      '/^calendar\/api\/get-shared-calendars\/?$/' => Controllers\Api\GetSharedCalendars::class,
      '/^calendar\/api\/stop-sharing-calendar\/?$/' => Controllers\Api\StopSharingCalendar::class,
    ]);

    $boards = $this->getService(\HubletoApp\Community\Dashboards\Manager::class);
    $boards->addBoard(
      $this,
      $this->translate('Reminders'),
      'calendar/boards/reminders'
    );

    /** @var \HubletoApp\Community\Calendar\Manager $calendarManager */
    $calendarManager = $this->getService(\HubletoApp\Community\Calendar\Manager::class);
    $calendarManager->addCalendar($this, 'calendar', 'blue', Calendar::class);

  }

  public function installTables(int $round): void
  {
    if ($round == 1) {
      $mActivity = $this->getService(Activity::class);
      $mActivity->install();
      $mSharedCalendar = $this->getService(SharedCalendar::class);
      $mSharedCalendar->install();
    }
  }

}
