<?php

namespace HubletoApp\Community\Calendar\Controllers;

class Calendar extends \HubletoMain\Core\Controller {


  public function getBreadcrumbs(): array
  {
    return array_merge(parent::getBreadcrumbs(), [
      [ 'url' => 'calendar', 'content' => $this->translate('Calendar') ],
    ]);
  }

  public function prepareView(): void
  {
    parent::prepareView();
    $calendarManager = $this->main->apps->getAppInstance(\HubletoApp\Community\Calendar::class)->calendarManager;
    foreach ($calendarManager->getCalendars() as $calendarClass => $calendar) {
      if ($calendarClass == "HubletoApp\Community\CalendarSync\Calendar") continue;
      $this->viewParams["calendarConfigs"][] = $calendar->activitySelectorConfig;
    }
    $this->setView('@HubletoApp:Community:Calendar/Calendar.twig');
  }

}