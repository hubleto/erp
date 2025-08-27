<?php

namespace Hubleto\App\Community\EventFeedback\Controllers;

class Dashboard extends \Hubleto\Erp\Controller
{
  public function getBreadcrumbs(): array
  {
    return array_merge(parent::getBreadcrumbs(), [
      [ 'url' => 'eventfeedback', 'content' => 'EventFeedback' ],
    ]);
  }

  public function prepareView(): void
  {
    parent::prepareView();
    $this->viewParams['now'] = date('Y-m-d H:i:s');
    $this->setView('@Hubleto:App:Community:EventFeedback/Dashboard.twig');
  }

}
