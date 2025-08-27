<?php

namespace Hubleto\App\Community\Calendar\Controllers;

use Hubleto\App\Community\Calendar\Models\RecordManagers\SharedCalendar;

class Settings extends \Hubleto\Erp\Controller
{
  public function getBreadcrumbs(): array
  {
    return array_merge(parent::getBreadcrumbs(), [
      [ 'url' => 'calendar', 'content' => $this->translate('Calendar') ],
      [ 'url' => 'settings', 'content' => $this->translate('Settings') ],
    ]);
  }

  public function prepareView(): void
  {
    parent::prepareView();

    /** @var \Hubleto\App\Community\Calendar\Manager $calendarManager */
    $calendarManager = $this->getService(\Hubleto\App\Community\Calendar\Manager::class);

    $mSharedCalendar = new SharedCalendar();

    foreach ($calendarManager->getCalendars() as $source => $calendar) {
      $calendarConfig = $calendar->calendarConfig;
      $calendarConfig['color'] = $calendar->getColor();
      $calendarConfig['shared'] = $mSharedCalendar->where('calendar', $source)->count();
      $this->viewParams["calendarConfigs"][$source] = $calendarConfig;
    }
    $this->setView('@Hubleto:App:Community:Calendar/Settings.twig');
  }

}
