<?php

namespace Hubleto\App\Community\Projects\Controllers;

class ProjectsOrders extends \Hubleto\Erp\Controller
{
  public function prepareView(): void
  {
    parent::prepareView();
    $this->setView('@Hubleto:App:Community:Projects/ProjectsOrders.twig');
  }

}
