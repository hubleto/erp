<?php

namespace Hubleto\App\Community\EventRegistrations\Controllers;

class Settings extends \Hubleto\Erp\Controller
{
  public function getBreadcrumbs(): array
  {
    return array_merge(parent::getBreadcrumbs(), [
      [ 'url' => 'eventregistrations', 'content' => 'EventRegistrations' ],
      [ 'url' => 'settings', 'content' => 'Settings' ],
    ]);
  }

  public function prepareView(): void
  {
    parent::prepareView();
    $this->setView('@Hubleto:App:Community:EventRegistrations/Settings.twig');
  }

}
