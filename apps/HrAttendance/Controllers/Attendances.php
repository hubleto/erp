<?php

namespace Hubleto\App\Community\HrAttendance\Controllers;

class Attendances extends \Hubleto\Erp\Controller
{
  public function prepareView(): void
  {
    parent::prepareView();
    $this->setView('@Hubleto:App:Community:HrAttendance/Attendances.twig');
  }
}