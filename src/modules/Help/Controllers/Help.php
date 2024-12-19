<?php

namespace CeremonyCrmMod\Help\Controllers;

class Help extends \CeremonyCrmApp\Core\Controller {

  public function prepareView(): void
  {
    parent::prepareView();
    $this->setView('@mod/Help/Views/Help.twig');
  }

}