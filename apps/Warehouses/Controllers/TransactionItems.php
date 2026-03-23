<?php

namespace Hubleto\App\Community\Warehouses\Controllers;

class TransactionItems extends \Hubleto\Erp\Controller
{

  public function getBreadcrumbs(): array
  {
    return array_merge(parent::getBreadcrumbs(), [
      [ 'url' => 'warehouses', 'content' => $this->translate('Warehouses') ],
      [ 'url' => 'transactions', 'content' => $this->translate('Transactions') ],
      [ 'url' => 'transactions/items', 'content' => $this->translate('Items') ],
    ]);
  }

  public function prepareView(): void
  {
    parent::prepareView();
    $this->setView('@Hubleto:App:Community:Warehouses/TransactionItems.twig');
  }

}
