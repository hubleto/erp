<?php

namespace Hubleto\App\Community\Settings\Controllers;

class General extends \Hubleto\Erp\Controller
{
  public function prepareView(): void
  {
    parent::prepareView();
    $this->setView('@Hubleto:App:Community:Settings/General.twig');
  }

}
