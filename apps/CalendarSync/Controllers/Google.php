<?php

namespace Hubleto\App\Community\CalendarSync\Controllers;

class Google extends \Hubleto\Erp\Controller
{
  public function prepareView(): void
  {
    parent::prepareView();
    $this->viewParams['now'] = date('Y-m-d H:i:s');
    $this->setView('@Hubleto:App:Community:CalendarSync/google.twig');
  }

  public function getBreadcrumbs(): array
  {
    return array_merge(parent::getBreadcrumbs(), [
      [ 'url' => 'settings', 'content' => $this->translate('Settings') ],
      [ 'url' => 'settings/calendar-sources', 'content' => $this->translate('Calendar Sources') ],
      [ 'url' => '', 'content' => $this->translate('Google Calendars') ],
    ]);
  }
}
