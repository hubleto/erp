<?php

namespace HubletoApp\Community\Settings\Controllers;

class Config extends \HubletoMain\Core\Controller {


  public function prepareView(): void
  {
    parent::prepareView();
    $this->setView('@app/community/Settings/Views/Config.twig');
  }

}