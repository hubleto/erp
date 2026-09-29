<?php

namespace Hubleto\App\Community\HrEmployees\Controllers;

class WorkLocations extends \Hubleto\Erp\Controller
{
  public function prepareView(): void
  {
    parent::prepareView();
    $this->viewParams['model'] = 'Hubleto/App/Community/HrEmployees/Models/WorkLocation';
    $this->viewParams['baseUrlSlug'] = 'hr-employees/work-locations';
    $this->setView('@Hubleto:App:Community:HrEmployees/WorkLocations.twig');
  }
}