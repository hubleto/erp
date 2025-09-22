<?php

namespace Hubleto\App\Community\ChartOfAccounts\Controllers;

class Accounts extends \Hubleto\Erp\Controller
{
  public function getBreadcrumbs(): array
  {
    return array_merge(parent::getBreadcrumbs(), [
      [ 'url' => 'chart-of-accounts/accounts', 'content' => $this->translate('Accounts') ],
    ]);
  }

  public function prepareView(): void
  {
    parent::prepareView();
    $this->setView('@Hubleto:App:Community:ChartOfAccounts/Accounts.twig');
  }

}
