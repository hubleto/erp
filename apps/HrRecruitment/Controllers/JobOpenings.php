<?php

namespace Hubleto\App\Community\HrRecruitment\Controllers;

class JobOpenings extends \Hubleto\Erp\Controller
{
  public function prepareView(): void
  {
    parent::prepareView();
    $this->setView('@Hubleto:App:Community:HrRecruitment/JobOpenings.twig');
  }
}