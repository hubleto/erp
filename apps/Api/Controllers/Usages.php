<?php

namespace Hubleto\App\Community\Api\Controllers;

class Usages extends \Hubleto\Erp\Controller
{

  public function prepareView(): void
  {
    parent::prepareView();
    $this->setView('@Hubleto:App:Community:Api/Usages.twig');
  }

}
