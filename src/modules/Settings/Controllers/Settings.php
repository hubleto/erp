<?php

namespace CeremonyCrmMod\Settings\Controllers;

class Settings extends \CeremonyCrmApp\Core\Controller {


  public function prepareView(): void
  {
    parent::prepareView();
    $this->setView('@mod/Settings/Views/Settings.twig');
  }

}