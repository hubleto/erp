<?php

namespace CeremonyCrmApp\Modules\Core\Dashboard\Controllers;

class Home extends \CeremonyCrmApp\Core\Controller {
  public function prepareView(): void
  {
    parent::prepareView();
    $this->setView('@app/Modules/Core/Dashboard/Views/Home.twig');
  }
}