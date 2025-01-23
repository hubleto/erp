<?php

namespace HubletoApp\Community\Calendar;

class Loader extends \HubletoMain\Core\App
{

  public function __construct(\HubletoMain $main)
  {
    parent::__construct($main);
  }

  public function init(): void
  {
    $this->main->router->httpGet([
      '/^calendar\/?$/' => Controllers\Calendar::class,
      '/^calendar\/get-calendar-events\/?$/' => Controllers\Api\GetCalendarEvents::class,
    ]);

    $this->main->sidebar->addLink(1, 50, 'calendar', $this->translate('Calendar'), 'fas fa-calendar-days', str_starts_with($this->main->requestedUri, 'calendar'));
  }

  public function installDefaultPermissions()
  {
    $mPermission = new \HubletoApp\Community\Settings\Models\Permission($this->main);
    $permissions = [
      "HubletoApp/Community/Calendar/Calendar" => "Calendar",
      "HubletoApp/Community/Calendar/Controllers/Calendar" => "Calendar/Controller",
      "HubletoApp/Community/Calendar/Api/GetCalendarEvents" => "Calendar/Api/GetCalendarEvents",
    ];

    foreach ($permissions as $permission => $allias) {
      $mPermission->eloquent->create([
        "permission" => $permission,
        "allias" => $allias,
      ]);
    }
  }

}