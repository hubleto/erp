<?php

namespace HubletoApp\Community\Worksheets\Controllers;

class Home extends \Hubleto\Erp\Controller
{
  public function prepareView(): void
  {
    parent::prepareView();
    $this->setView('@HubletoApp:Community:Worksheets/Home.twig');
  }

}
