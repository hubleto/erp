<?php

namespace HubletoApp\Community\Premium\Controllers;

class Log extends \HubletoMain\Core\Controllers\Controller {

  public function prepareView(): void
  {
    parent::prepareView();

    $this->setView('@HubletoApp:Community:Premium/Log.twig');
  }

}