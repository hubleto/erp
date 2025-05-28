<?php

namespace HubletoApp\Community\Cloud\Controllers;

class Payments extends \HubletoMain\Core\Controllers\Controller {

  public function prepareView(): void
  {
    parent::prepareView();
    $this->setView('@HubletoApp:Community:Cloud/Payments.twig');
  }

}