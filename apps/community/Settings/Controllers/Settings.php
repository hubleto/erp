<?php

namespace HubletoApp\Community\Settings\Controllers;

class Settings extends \HubletoMain\Core\Controller {


  public function prepareView(): void
  {
    parent::prepareView();
    $this->setView('@app/community/Settings/Views/Settings.twig');
  }

}