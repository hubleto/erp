<?php

namespace Hubleto\App\Community\Worksheets\Controllers;

class Home extends \Hubleto\Erp\Controller
{
  public function prepareView(): void
  {
    parent::prepareView();
    $this->setView('@Hubleto:App:Community:Worksheets/Home.twig');
  }

}
