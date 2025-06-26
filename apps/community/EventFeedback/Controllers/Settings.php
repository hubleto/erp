<?php

namespace HubletoApp\Community\EventFeedback\Controllers;

class Settings extends \HubletoMain\Core\Controllers\Controller
{

  public function getBreadcrumbs(): array
  {
    return array_merge(parent::getBreadcrumbs(), [
      [ 'url' => 'eventfeedback', 'content' => 'EventFeedback' ],
      [ 'url' => 'settings', 'content' => 'Settings' ],
    ]);
  }

  public function prepareView(): void
  {
    parent::prepareView();
    $this->setView('@HubletoApp:Community:EventFeedback/Settings.twig');
  }

}