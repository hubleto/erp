<?php

namespace Hubleto\App\Community\HrLeave\Controllers;

class Leaves extends \Hubleto\Erp\Controller
{
  public function prepareView(): void
  {
    parent::prepareView();
    $this->setView('@Hubleto:App:Community:HrLeave/Leaves.twig');
  }
}