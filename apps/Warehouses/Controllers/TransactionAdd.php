<?php

namespace Hubleto\App\Community\Warehouses\Controllers;

class TransactionAdd extends \Hubleto\Erp\Controller
{
  public function prepareView(): void
  {
    parent::prepareView();
    $this->setView('@Hubleto:App:Community:Warehouses/TransactionAdd.twig');
  }

}
