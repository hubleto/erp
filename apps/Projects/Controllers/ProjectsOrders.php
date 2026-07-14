<?php

namespace Hubleto\App\Community\Projects\Controllers;

class ProjectsOrders extends \Hubleto\Erp\Controller
{

  public function getBreadcrumbs(): array
  {
    return array_merge(parent::getBreadcrumbs(), [
      [ 'content' => $this->translate('Assign project to order') ],
    ]);
  }

  public function prepareView(): void
  {
    parent::prepareView();
    $this->setView('@Hubleto:App:Community:Projects/ProjectsOrders.twig');
  }

}
