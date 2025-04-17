<?php

namespace HubletoApp\Community\Settings\Controllers;

class General extends \HubletoMain\Core\Controllers\Controller {


  public function prepareView(): void
  {
    parent::prepareView();
    $this->setView('@HubletoApp:Community:Settings/General.twig');
  }

}