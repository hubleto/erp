<?php

namespace HubletoApp\Community\EventFeedback\Controllers;

class Dashboard extends \HubletoMain\Controller
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
    $this->setView('@HubletoApp:Community:EventFeedback/Dashboard.twig');
  }

}
