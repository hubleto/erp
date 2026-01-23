<?php

namespace Hubleto\App\Community\Calendar;

use Hubleto\App\Community\Calendar\Models\Activity;
use Hubleto\App\Community\Calendar\Models\SharedCalendar;

class Loader extends \Hubleto\Framework\App
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
      '/^calendar\/api\/get-calendar-events\/?$/' => Controllers\Api\GetCalendarEvents::class,
      '/^calendar\/api\/get-shared-calendars\/?$/' => Controllers\Api\GetSharedCalendars::class,
      '/^calendar\/api\/stop-sharing-calendar\/?$/' => Controllers\Api\StopSharingCalendar::class,
      '/^calendar\/api\/set-initial-view\/?$/' => Controllers\Api\SetInitialView::class,

      '/^calendar\/?$/' => Controllers\Calendar::class,
      '/^calendar(\/(?<key>\w+))?\/ics\/?$/' => Controllers\IcsCalendar::class,
      '/^calendar\/share\/?$/' => Controllers\Share::class,
      '/^calendar\/boards\/reminders\/?$/' => Controllers\Boards\Reminders::class,
    ]);

    $boards = $this->getService(\Hubleto\App\Community\Dashboards\Manager::class);
    $boards->addBoard(
      $this,
      $this->translate('Reminders'),
      'calendar/boards/reminders'
    );

    /** @var \Hubleto\App\Community\Calendar\Manager $calendarManager */
    $calendarManager = $this->getService(\Hubleto\App\Community\Calendar\Manager::class);
    $calendarManager->addCalendar($this, 'calendar', 'blue', Calendar::class);

  }

  public function installTables(int $round): void
  {
    if ($round == 1) {
      $mActivity = $this->getModel(Activity::class);
      $mActivity->install();
      $mSharedCalendar = $this->getModel(SharedCalendar::class);
      $mSharedCalendar->install();
    }
  }

  public function getInitialView(): string
  {
    $initialView = $this->config()->getAsString('apps/Hubleto\\App\\Community\\Calendar/initialView', 'timeGridWeek');
    if (!in_array($initialView, ['timeGridDay', 'timeGridWeek', 'dayGridMonth', 'listYear'])) $initialView = 'timeGridWeek';
    return $initialView;
  }

  public function setInitialView(string $initialView): void
  {
    if (!in_array($initialView, ['timeGridDay', 'timeGridWeek', 'dayGridMonth', 'listYear'])) $initialView = 'timeGridWeek';
    $this->config()->save('apps/Hubleto\\App\\Community\\Calendar/initialView', $initialView);
  }

}
