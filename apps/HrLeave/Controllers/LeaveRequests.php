<?php

namespace Hubleto\App\Community\HrLeave\Controllers;

class LeaveRequests extends \Hubleto\Erp\Controller
{
  public function prepareView(): void
  {
    parent::prepareView();
    $this->setView('@Hubleto:App:Community:HrLeave/LeaveRequests.twig');
  }
}