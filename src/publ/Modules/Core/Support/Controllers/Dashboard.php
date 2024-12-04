<?php

namespace CeremonyCrmApp\Modules\Core\Support\Controllers;

class Dashboard extends \CeremonyCrmApp\Core\Controller {

  public function prepareView(): void
  {
    parent::prepareView();
    $this->setView('@app/Modules/Core/Support/Views/Dashboard.twig');
  }

}