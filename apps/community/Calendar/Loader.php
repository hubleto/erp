<?php

namespace HubletoApp\Community\Calendar;

class Loader extends \HubletoMain\Core\App
{

  public function init(): void
  {
    parent::init();

    $this->main->router->httpGet([
      '/^calendar\/?$/' => Controllers\Calendar::class,
      '/^calendar\/get-calendar-events\/?$/' => Controllers\Api\GetCalendarEvents::class,
    ]);

    $this->main->help->addContextHelpUrls('/^calendar\/?$/', [
      'en' => 'en/apps/community/calendar',
    ]);
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