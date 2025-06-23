<?php

namespace HubletoApp\Community\CalendarActivities\Controllers;

class Settings extends \HubletoMain\Core\Controllers\Controller
{

  public function getBreadcrumbs(): array
  {
    return array_merge(parent::getBreadcrumbs(), [
      [ 'url' => 'calendaractivities', 'content' => 'CalendarActivities' ],
      [ 'url' => 'settings', 'content' => 'Settings' ],
    ]);
  }

  public function prepareView(): void
  {
    parent::prepareView();
    $this->setView('@HubletoApp:Community:CalendarActivities/Settings.twig');
  }

}