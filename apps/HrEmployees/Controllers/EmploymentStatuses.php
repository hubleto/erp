<?php

namespace Hubleto\App\Community\HrEmployees\Controllers;

class EmploymentStatuses extends \Hubleto\Erp\Controller
{
  public function prepareView(): void
  {
    parent::prepareView();
    $this->viewParams['model'] = 'Hubleto/App/Community/HrEmployees/Models/EmploymentStatus';
    $this->viewParams['baseUrlSlug'] = 'hr-employees/employment-statuses';
    $this->setView('@Hubleto:App:Community:HrEmployees/EmploymentStatuses.twig');
  }
}