<?php

namespace HubletoApp\Community\CalendarSync\Controllers;

class Ics extends \HubletoMain\Core\Controllers\Controller {
  public function prepareView(): void {
    parent::prepareView();
    $this->viewParams['now'] = date('Y-m-d H:i:s');
    $this->setView('@HubletoApp:Community:CalendarSync/ics.twig');

    // $mSource = new Source($this->app);
    // $mSource->install();
  }

  public function getBreadcrumbs(): array
  {
    return array_merge(parent::getBreadcrumbs(), [
      [ 'url' => 'settings', 'content' => $this->translate('Settings') ],
      [ 'url' => 'settings/calendar-sources', 'content' => $this->translate('Calendar Sources') ],
      [ 'url' => '', 'content' => $this->translate('.ics Calendars') ],
    ]);
  }
}