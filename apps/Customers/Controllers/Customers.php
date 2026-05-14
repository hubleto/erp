<?php

namespace Hubleto\App\Community\Customers\Controllers;

class Customers extends \Hubleto\Erp\Controller
{

  public function prepareView(): void
  {
    parent::prepareView();
    $this->setView('@Hubleto:App:Community:Customers/Customers.twig');
  }

}
