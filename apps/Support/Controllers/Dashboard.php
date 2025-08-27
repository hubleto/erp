<?php

namespace HubletoApp\Community\Support\Controllers;

class Dashboard extends \Hubleto\Erp\Controller
{
  public function prepareView(): void
  {
    parent::prepareView();
    $this->setView('@HubletoApp:Community:Support/Dashboard.twig');
  }

}
