<?php

namespace HubletoApp\Community\Calendar\Controllers;

class Calendar extends \Hubleto\Erp\Controller
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

    /** @var \HubletoApp\Community\Calendar\Manager $calendarManager */
    $calendarManager = $this->getService(\HubletoApp\Community\Calendar\Manager::class);

    foreach ($calendarManager->getCalendars() as $source => $calendar) {
      $calendarConfig = $calendar->calendarConfig;
      $calendarConfig['color'] = $calendar->getColor();
      $this->viewParams["calendarConfigs"][$source] = $calendarConfig;
    }
    $this->setView('@HubletoApp:Community:Calendar/Calendar.twig');
  }

}
