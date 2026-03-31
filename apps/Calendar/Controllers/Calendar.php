<?php

namespace Hubleto\App\Community\Calendar\Controllers;

use \Hubleto\App\Community\Calendar\Manager;

class Calendar extends \Hubleto\Erp\Controller
{
  public function getBreadcrumbs(): array
  {
    return array_merge(parent::getBreadcrumbs(), [
      // [ 'url' => 'calendar', 'content' => $this->translate('Calendar') ],
    ]);
  }

  public function prepareView(): void
  {
    parent::prepareView();

    $show = $this->router()->urlParamAsString('show');

    /** @var \Hubleto\App\Community\Calendar\Loader */
    $calendarApp = $this->getService(\Hubleto\App\Community\Calendar\Loader::class);

    /** @var Controllers\Api\GetCalendarEvents */
    $getCalendarEvents = $this->getController(Api\GetCalendarEvents::class);

    $this->viewParams['initialView'] = $calendarApp->getInitialView();

    /** @var Manager */
    $calendarManager = $this->getService(Manager::class);

    foreach ($calendarManager->getCalendars() as $calendarName => $calendar) {
      $calendarConfig = $calendar->getCalendarConfig();
      $calendarConfig['color'] = $calendar->getColor();
      $calendarConfig['show'] = empty($show) || $show == $calendarName;

      $this->viewParams["calendars"][$calendarName] = $calendarConfig;
    }

    $this->setView('@Hubleto:App:Community:Calendar/Calendar.twig');
  }

}
