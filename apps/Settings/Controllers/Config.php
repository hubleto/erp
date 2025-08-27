<?php

namespace HubletoApp\Community\Settings\Controllers;

class Config extends \Hubleto\Erp\Controller
{
  public function prepareView(): void
  {
    parent::prepareView();
    $this->setView('@HubletoApp:Community:Settings/Config.twig');
  }

}
