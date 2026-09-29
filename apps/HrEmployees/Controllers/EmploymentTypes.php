<?php

namespace Hubleto\App\Community\HrEmployees\Controllers;

class EmploymentTypes extends \Hubleto\Erp\Controller
{
  public function prepareView(): void
  {
    parent::prepareView();
    $this->viewParams['model'] = 'Hubleto/App/Community/HrEmployees/Models/EmploymentType';
    $this->viewParams['baseUrlSlug'] = 'hr-employees/employment-types';
    $this->setView('@Hubleto:App:Community:HrEmployees/EmploymentTypes.twig');
  }
}