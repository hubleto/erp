<?php

namespace HubletoApp\Community\Calendar\Controllers;

class Calendar extends \HubletoMain\Controller
{
  public function getBreadcrumbs(): array
  {
    return array_merge(parent::getBreadcrumbs(), [
      [ 'url' => 'calendar', 'content' => $this->translate('Calendar') ],
    ]);
  }

  public function prepareView(): void
  {
    parent::prepareView();
    $calendarManager = $this->main->apps->community('Calendar')->calendarManager;
    foreach ($calendarManager->getCalendars() as $source => $calendar) {
      $calendarConfig = $calendar->calendarConfig;
      $calendarConfig['color'] = $calendar->getColor();
      $this->viewParams["calendarConfigs"][$source] = $calendarConfig;
    }
    $this->setView('@HubletoApp:Community:Calendar/Calendar.twig');
  }

}
