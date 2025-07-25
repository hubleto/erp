<?php

namespace HubletoApp\Community\Calendar\Controllers;

use HubletoApp\Community\Calendar\Models\RecordManagers\SharedCalendar;

class Settings extends \HubletoMain\Controller
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
    $calendarManager = $this->main->apps->community('Calendar')->calendarManager;
    $mSharedCalendar = new SharedCalendar();
    foreach ($calendarManager->getCalendars() as $source => $calendar) {
      $calendarConfig = $calendar->calendarConfig;
      $calendarConfig['color'] = $calendar->getColor();
      $calendarConfig['shared'] = $mSharedCalendar->where('calendar', $source)->count();
      $this->viewParams["calendarConfigs"][$source] = $calendarConfig;
    }
    $this->setView('@HubletoApp:Community:Calendar/Settings.twig');
  }

}
