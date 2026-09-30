<?php

namespace Hubleto\App\Community\HrRecruitment\Controllers;

class Candidates extends \Hubleto\Erp\Controller
{
  public function prepareView(): void
  {
    parent::prepareView();
    $this->setView('@Hubleto:App:Community:HrRecruitment/Candidates.twig');
  }
}