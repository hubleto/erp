<?php

namespace HubletoApp\Community\Calendar;

use HubletoApp\Community\Calendar\Models\Activity;

class Loader extends \HubletoMain\Core\App
{

  public CalendarManager $calendarManager;

   public bool $hasCustomSettings = true;

  public function __construct(\HubletoMain $main)
  {
    parent::__construct($main);
    $this->calendarManager = new CalendarManager($main);
  }

  public function init(): void
  {
    parent::init();

    $this->main->router->httpGet([
      '/^calendar\/?$/' => Controllers\Calendar::class,
      '/^calendar\/ics\/?$/' => Controllers\IcsCalendar::class,
      '/^calendar\/settings\/?$/' => Controllers\Settings::class,
      '/^calendar\/boards\/reminders\/?$/' => Controllers\Boards\Reminders::class,
      '/^calendar\/api\/get-calendar-events\/?$/' => Controllers\Api\GetCalendarEvents::class,
      '/^calendar\/api\/share-calendar\/?$/' => Controllers\Api\ShareCalendar::class,
    ]);

    $this->main->apps->community('Help')?->addContextHelpUrls('/^calendar\/?$/', [
      'en' => 'en/apps/community/calendar',
    ]);

    $this->main->apps->community('Dashboards')?->addBoard(
      $this,
      $this->translate('Reminders'),
      'calendar/boards/reminders'
    );

    $this->main->apps->community('Calendar')?->calendarManager?->addCalendar(
      'calendar',
      'blue',
      Calendar::class
    );

  }

  public function installTables(int $round): void
  {
    if ($round == 1) {
      $mActivity = new Activity($this->main);
      $mActivity->install();
    }
  }

  // public function installDefaultPermissions(): void
  // {
  //   $mPermission = new \HubletoApp\Community\Settings\Models\Permission($this->main);
  //   $permissions = [
  //     "HubletoApp/Community/Calendar/Calendar",
  //     "HubletoApp/Community/Calendar/Controllers/Calendar",
  //     "HubletoApp/Community/Calendar/Api/GetCalendarEvents",
  //   ];

  //   foreach ($permissions as $permission) {
  //     $mPermission->record->recordCreate([
  //       "permission" => $permission
  //     ]);
  //   }
  // }

}