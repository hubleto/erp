<?php

namespace CeremonyCrmMod\Support\Controllers;

class Dashboard extends \CeremonyCrmApp\Core\Controller {

  public function prepareView(): void
  {
    parent::prepareView();
    $this->setView('@mod/Support/Views/Dashboard.twig');
  }

}