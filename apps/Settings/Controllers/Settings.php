<?php

namespace HubletoApp\Settings\Controllers;

class Settings extends \HubletoMain\Core\Controller {


  public function prepareView(): void
  {
    parent::prepareView();
    $this->setView('@app/Settings/Views/Settings.twig');
  }

}