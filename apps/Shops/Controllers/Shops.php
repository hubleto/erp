<?php

namespace Hubleto\App\Community\Shops\Controllers;

class Shops extends \Hubleto\Erp\Controller
{
  public function prepareView(): void
  {
    parent::prepareView();

    $this->setView('@Hubleto:App:Community:Shops/Shops.twig');
  }

}
