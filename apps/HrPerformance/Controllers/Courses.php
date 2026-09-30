<?php

namespace Hubleto\App\Community\HrPerformance\Controllers;

class Courses extends \Hubleto\Erp\Controller
{
  public function prepareView(): void
  {
    parent::prepareView();
    $this->setView('@Hubleto:App:Community:HrPerformance/Courses.twig');
  }
}