<?php

namespace HubletoApp\Community\CalendarActivities\Controllers;

class Dashboard extends \HubletoMain\Core\Controllers\Controller
{

  public function getBreadcrumbs(): array
  {
    return array_merge(parent::getBreadcrumbs(), [
      [ 'url' => 'calendaractivities', 'content' => 'CalendarActivities' ],
    ]);
  }

  public function prepareView(): void
  {
    parent::prepareView();
    $this->viewParams['now'] = date('Y-m-d H:i:s');
    $this->setView('@HubletoApp:Community:CalendarActivities/Dashboard.twig');
  }

}