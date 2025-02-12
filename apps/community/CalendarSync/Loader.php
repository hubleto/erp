<?php

namespace HubletoApp\Community\CalendarSync;

use HubletoApp\Community\CalendarSync\Controllers\Sources;

class Loader extends \HubletoMain\Core\App
{

  public function init(): void
  {
    parent::init();

    $this->main->router->httpGet([
      '/^calendar-sources\/?$/' => Sources::class,
    ]);

    $this->main->sidebar->addLink(1, 51, 'calendar-sources', $this->translate('Sources'), 'fas fa-calendar', str_starts_with($this->main->requestedUri, 'calendar-sources/') || $this->main->requestedUri == 'calendar-sources');

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

      "HubletoApp/Community/Calendar/Controllers/Sources",
    ];

    foreach ($permissions as $permission) {
      $mPermission->eloquent->create([
        "permission" => $permission
      ]);
    }
  }

}