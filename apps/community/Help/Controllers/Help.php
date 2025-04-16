<?php

namespace HubletoApp\Community\Help\Controllers;

class Help extends \HubletoMain\Core\Controllers\Controller {

  public function prepareView(): void
  {
    parent::prepareView();
    $this->setView('@HubletoApp:Community:Help/Help.twig');
  }

}