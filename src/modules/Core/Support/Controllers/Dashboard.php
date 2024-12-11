<?php

namespace CeremonyCrmMod\Core\Support\Controllers;

class Dashboard extends \CeremonyCrmApp\Core\Controller {

  public function prepareView(): void
  {
    parent::prepareView();
    $this->setView('@mod/Core/Support/Views/Dashboard.twig');
  }

}