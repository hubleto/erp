<?php

namespace HubletoApp\Community\Calendar;

use HubletoApp\Community\Calendar\Models\Activity;

class Loader extends \HubletoMain\Core\App
{

  public CalendarManager $calendarManager;

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
      '/^calendar\/get-calendar-events\/?$/' => Controllers\Api\GetCalendarEvents::class,
    ]);

    $this->main->apps->getAppInstance(\HubletoApp\Community\Help::class)->addContextHelpUrls('/^calendar\/?$/', [
      'en' => 'en/apps/community/calendar',
    ]);
  }

  public function installTables(int $round): void
  {
    if ($round == 1) {
      $mActivity = new Activity($this->main);
      $mActivity->install();
    }
  }

  public function installDefaultPermissions(): void
  {
    $mPermission = new \HubletoApp\Community\Settings\Models\Permission($this->main);
    $permissions = [
      "HubletoApp/Community/Calendar/Calendar",
      "HubletoApp/Community/Calendar/Controllers/Calendar",
      "HubletoApp/Community/Calendar/Api/GetCalendarEvents",
    ];

    foreach ($permissions as $permission) {
      $mPermission->eloquent->create([
        "permission" => $permission
      ]);
    }
  }

}