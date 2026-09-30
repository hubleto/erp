<?php

namespace Hubleto\App\Community\HrRecruitment\Controllers;

class Applications extends \Hubleto\Erp\Controller
{
  public function prepareView(): void
  {
    parent::prepareView();
    $this->setView('@Hubleto:App:Community:HrRecruitment/Applications.twig');
  }
}