<?php

namespace HubletoApp\Community\Inventory\Controllers;

class Transactions extends \HubletoMain\Core\Controllers\Controller
{

  public function getBreadcrumbs(): array
  {
    return array_merge(parent::getBreadcrumbs(), [
      [ 'url' => 'inventory', 'content' => 'Inventory' ],
      [ 'url' => 'transactions', 'content' => 'Transactions' ],
    ]);
  }

  public function prepareView(): void
  {
    parent::prepareView();
    $this->setView('@HubletoApp:Community:Inventory/Transactions.twig');
  }

}