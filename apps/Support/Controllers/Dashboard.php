<?php

namespace Hubleto\App\Community\Support\Controllers;

class Dashboard extends \Hubleto\Erp\Controller
{
  public function prepareView(): void
  {
    parent::prepareView();
    $this->setView('@Hubleto:App:Community:Support/Dashboard.twig');
  }

}
