<?php

namespace Hubleto\App\Community\HrEmployees\Controllers;

class EmploymentStatuses extends \Hubleto\Erp\Controller
{
  public function prepareView(): void
  {
    parent::prepareView();
    $this->setView('@Hubleto:App:Community:HrEmployees/EmploymentStatuses.twig');
  }
}