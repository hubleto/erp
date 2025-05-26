<?php

namespace HubletoApp\Community\Premium\Controllers;

class MakePayment extends \HubletoMain\Core\Controllers\Controller {

  public function prepareView(): void
  {
    parent::prepareView();

    $this->setView('@HubletoApp:Community:Premium/MakePayment.twig');
  }

}