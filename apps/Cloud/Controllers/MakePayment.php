<?php

namespace HubletoApp\Community\Cloud\Controllers;

class MakePayment extends \HubletoMain\Controller
{
  public function prepareView(): void
  {
    parent::prepareView();

    $this->setView('@HubletoApp:Community:Cloud/MakePayment.twig');
  }

}
