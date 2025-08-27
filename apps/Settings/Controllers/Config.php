<?php

namespace Hubleto\App\Community\Settings\Controllers;

class Config extends \Hubleto\Erp\Controller
{
  public function prepareView(): void
  {
    parent::prepareView();
    $this->setView('@Hubleto:App:Community:Settings/Config.twig');
  }

}
