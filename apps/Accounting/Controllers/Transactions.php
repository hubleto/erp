<?php

namespace Hubleto\App\Community\Accounting\Controllers;

class Transactions extends \Hubleto\Erp\Controller
{
  public function getBreadcrumbs(): array
  {
    return array_merge(parent::getBreadcrumbs(), [
      [ 'url' => 'transactions', 'content' => $this->translate('List') ],
    ]);
  }

  public function prepareView(): void
  {
    parent::prepareView();
    $this->setView('@Hubleto:App:Community:Accounting/Transactions.twig');
  }

}
