<?php

namespace HubletoApp\Community\CalendarSync;

use HubletoApp\Community\CalendarSync\Controllers\Google;
use HubletoApp\Community\CalendarSync\Controllers\Home;
use HubletoApp\Community\CalendarSync\Controllers\Ics;

class Loader extends \HubletoMain\Core\App
{

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

    if (str_starts_with($this->main->requestedUri, 'settings/calendar-sources')) {
      $this->main->sidebar->addHeading1(2, 310, $this->translate('Calendar sources'));
      $this->main->sidebar->addLink(2, 320, 'settings/calendar-sources', $this->translate('Overview'), 'fas fa-home');
      $this->main->sidebar->addLink(2, 330, 'settings/calendar-sources/google', $this->translate('Google Calendar'), 'fab fa-google');
      $this->main->sidebar->addLink(2, 340, 'settings/calendar-sources/ics', $this->translate('ICS'), 'fas fa-file');
      //$this->main->sidebar->addLink(2, 10203, 'customers/activities', $this->translate('Activities'), 'fas fa-users');
    }

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