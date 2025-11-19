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

    /** @var \Hubleto\App\Community\Calendar\Loader */
    $calendarApp = $this->getService(\Hubleto\App\Community\Calendar\Loader::class);

    $this->viewParams['initialView'] = $calendarApp->getInitialView();

    /** @var Manager */
    $calendarManager = $this->getService(Manager::class);

    foreach ($calendarManager->getCalendars() as $source => $calendar) {
      $calendarConfig = $calendar->calendarConfig;
      $calendarConfig['color'] = $calendar->getColor();
      $this->viewParams["calendarConfigs"][$source] = $calendarConfig;
    }

    $this->setView('@Hubleto:App:Community:Calendar/Calendar.twig');
  }

}
