<?php

namespace HubletoApp\Community\Settings\Controllers;

class Config extends \HubletoMain\Controller
{
  public function prepareView(): void
  {
    parent::prepareView();
    $this->setView('@HubletoApp:Community:Settings/Config.twig');
  }

}
