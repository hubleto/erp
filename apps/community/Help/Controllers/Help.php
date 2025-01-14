<?php

namespace HubletoApp\Community\Help\Controllers;

class Help extends \HubletoMain\Core\Controller {

  public function prepareView(): void
  {
    parent::prepareView();
    $this->setView('@app/community/Help/Views/Help.twig');
  }

}