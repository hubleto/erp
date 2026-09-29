<?php

namespace Hubleto\App\Community\HrEmployees\Controllers;

class Employees extends \Hubleto\Erp\Controller
{
  public function prepareView(): void
  {
    parent::prepareView();

    $this->viewParams['title'] = $this->translate('Employee profiles');
    $this->viewParams['model'] = 'Hubleto/App/Community/HrEmployees/Models/Employee';
    $this->viewParams['baseUrlSlug'] = 'hr-employees/employees';
    $this->setView('@Hubleto:App:Community:HrEmployees/Employees.twig');
  }
}