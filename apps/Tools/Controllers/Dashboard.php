<?php

namespace Hubleto\App\Community\Tools\Controllers;

class Dashboard extends \Hubleto\Erp\Controller
{
  public function getBreadcrumbs(): array
  {
    return array_merge(parent::getBreadcrumbs(), [
      [ 'url' => 'tools', 'content' => $this->translate('Tools') ],
    ]);
  }

  public function prepareView(): void
  {
    parent::prepareView();
    $this->viewParams['tools'] = $this->getService(\Hubleto\App\Community\Tools\Loader::class)->tools;
    $this->setView('@Hubleto:App:Community:Tools/Dashboard.twig');
  }

}
