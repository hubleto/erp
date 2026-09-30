<?php

namespace Hubleto\App\Community\HrPerformance\Controllers;

class Goals extends \Hubleto\Erp\Controller
{
  public function prepareView(): void
  {
    parent::prepareView();
    $this->setView('@Hubleto:App:Community:HrPerformance/Goals.twig');
  }
}