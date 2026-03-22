<?php

namespace Hubleto\App\Community\EventRegistrations\Controllers;

class Settings extends \Hubleto\Erp\Controller
{
  public function getBreadcrumbs(): array
  {
    return array_merge(parent::getBreadcrumbs(), [
      [ 'url' => 'eventregistrations', 'content' => $this->translate('EventRegistrations') ],
      [ 'url' => 'settings', 'content' => $this->translate('Settings') ],
    ]);
  }

  public function prepareView(): void
  {
    parent::prepareView();
    $this->setView('@Hubleto:App:Community:EventRegistrations/Settings.twig');
  }

}
