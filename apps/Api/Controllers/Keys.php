<?php

namespace Hubleto\App\Community\Api\Controllers;

class Keys extends \Hubleto\Erp\Controller
{

  public function prepareView(): void
  {
    parent::prepareView();
    $this->setView('@Hubleto:App:Community:Api/Keys.twig');
  }

}
