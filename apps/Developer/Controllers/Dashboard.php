<?php

namespace Hubleto\App\Community\Developer\Controllers;

class Dashboard extends \Hubleto\Erp\Controller
{
  public function getBreadcrumbs(): array
  {
    return array_merge(parent::getBreadcrumbs(), [
      [ 'url' => 'developer', 'content' => $this->translate('Developer') ],
    ]);
  }

  public function prepareView(): void
  {
    parent::prepareView();
    $this->viewParams['now'] = date('Y-m-d H:i:s');
    $this->setView('@Hubleto:App:Community:Developer/Dashboard.twig');
  }

}
