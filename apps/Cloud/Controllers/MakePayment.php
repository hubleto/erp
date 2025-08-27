<?php

namespace HubletoApp\Community\Cloud\Controllers;

class MakePayment extends \Hubleto\Erp\Controller
{
  public function prepareView(): void
  {
    parent::prepareView();

    $this->setView('@HubletoApp:Community:Cloud/MakePayment.twig');
  }

}
