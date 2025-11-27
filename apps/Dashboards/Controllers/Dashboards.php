<?php

namespace Hubleto\App\Community\Dashboards\Controllers;

class Dashboards extends \Hubleto\Erp\Controller
{
  public function getBreadcrumbs(): array
  {
    return array_merge(parent::getBreadcrumbs(), [
      // [ 'url' => 'settings', 'content' => $this->translate('Settings') ],
      // [ 'url' => 'dashboards/manage', 'content' => $this->translate('Dashboards') ],
    ]);
  }

  public function prepareView(): void
  {
    parent::prepareView();

    $this->setView('@Hubleto:App:Community:Dashboards/Dashboards.twig');
  }

}
