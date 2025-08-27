<?php

namespace Hubleto\App\Community\CalendarSync\Controllers;

class Home extends \Hubleto\Erp\Controller
{
  public function prepareView(): void
  {
    parent::prepareView();
    $this->viewParams['now'] = date('Y-m-d H:i:s');
    $this->setView('@Hubleto:App:Community:CalendarSync/home.twig');
  }

  public function getBreadcrumbs(): array
  {
    return array_merge(parent::getBreadcrumbs(), [
      [ 'url' => 'settings', 'content' => $this->translate('Settings') ],
      [ 'url' => '', 'content' => $this->translate('Calendar Sources') ],
    ]);
  }
}
