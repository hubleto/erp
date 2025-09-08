<?php

namespace Hubleto\App\Community\ChartOfAccounts\Controllers;

class AccountTypes extends \Hubleto\Erp\Controller
{
  public function getBreadcrumbs(): array
  {
    return array_merge(parent::getBreadcrumbs(), [
      [ 'url' => 'accounting', 'content' => $this->translate('Accounting') ],
      [ 'url' => 'accounting/account-types', 'content' => $this->translate('Account types') ],
    ]);
  }

  public function prepareView(): void
  {
    parent::prepareView();
    $this->setView('@Hubleto:App:Community:Accounting/AccountTypes.twig');
  }

}
