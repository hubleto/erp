<?php

namespace HubletoApp\Community\CalendarSync;

use HubletoApp\Community\CalendarSync\Controllers\Google;
use HubletoApp\Community\CalendarSync\Controllers\Home;
use HubletoApp\Community\CalendarSync\Controllers\Ics;

class Loader extends \HubletoMain\Core\App
{

  const DEFAULT_INSTALLATION_CONFIG = [
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

    $this->main->addSetting([
      'title' => $this->translate('Calendar sources'),
      'icon' => 'fas fa-calendar',
      'url' => 'settings/calendar-sources',
    ]);

    $this->setConfigAsInteger('sidebarOrder', 0);

    $this->main->calendarManager->addCalendar(Calendar::class);
  }

  public function installTables(): void
  {
    $mSource = new \HubletoApp\Community\CalendarSync\Models\Source($this->main);

    $mSource->dropTableIfExists()->install();
  }

  public function installDefaultPermissions(): void
  {
    $mPermission = new \HubletoApp\Community\Settings\Models\Permission($this->main);
    $permissions = [
      "HubletoApp/Community/CalendarSync/Source",
      "HubletoApp/Community/CalendarSync/Controllers/Home",
      "HubletoApp/Community/CalendarSync/Controllers/Google",
    ];

    foreach ($permissions as $permission) {
      $mPermission->eloquent->create([
        "permission" => $permission
      ]);
    }
  }

}