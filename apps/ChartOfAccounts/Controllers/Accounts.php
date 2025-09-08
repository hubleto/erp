<?php

namespace Hubleto\App\Community\ChartOfAccounts\Controllers;

class Accounts extends \Hubleto\Erp\Controller
{
  public function getBreadcrumbs(): array
  {
    return array_merge(parent::getBreadcrumbs(), [
      [ 'url' => 'accounting', 'content' => $this->translate('Accounting') ],
      [ 'url' => 'accounting/accounts', 'content' => $this->translate('Chart of accounts') ],
    ]);
  }

  public function prepareView(): void
  {
    parent::prepareView();
    $this->setView('@Hubleto:App:Community:ChartOfAccounts/Accounts.twig');
  }

}
