<?php

namespace Hubleto\App\Community\Warehouses\Controllers;

class Transactions extends \Hubleto\Erp\Controller
{

  public function getBreadcrumbs(): array
  {
    return array_merge(parent::getBreadcrumbs(), [
      // [ 'url' => 'warehouses', 'content' => 'Warehouses' ],
      [ 'url' => 'warehouses/transactions', 'content' => 'Transactions' ],
    ]);
  }

  public function prepareView(): void
  {
    parent::prepareView();
    $this->setView('@Hubleto:App:Community:Warehouses/Transactions.twig');
  }

}
