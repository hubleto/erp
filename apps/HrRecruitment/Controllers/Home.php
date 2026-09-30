<?php

namespace Hubleto\App\Community\HrRecruitment\Controllers;

class Home extends \Hubleto\Erp\Controller
{
  public function prepareView(): void
  {
    parent::prepareView();
    $this->setView('@Hubleto:App:Community:HrRecruitment/Home.twig');
  }
}