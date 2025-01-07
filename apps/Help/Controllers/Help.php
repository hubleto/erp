<?php

namespace HubletoApp\Help\Controllers;

class Help extends \HubletoMain\Core\Controller {

  public function prepareView(): void
  {
    parent::prepareView();
    $this->setView('@app/Help/Views/Help.twig');
  }

}