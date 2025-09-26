<?php

namespace Hubleto\App\Community\BkChartOfAccounts\Controllers;

class AccountSubtypes extends \Hubleto\Erp\Controller
{
  public function getBreadcrumbs(): array
  {
    return array_merge(parent::getBreadcrumbs(), [
      [ 'url' => 'accounting', 'content' => $this->translate('Accounting') ],
      [ 'url' => 'accounting/account-subtypes', 'content' => $this->translate('Account Subtypes') ],
    ]);
  }

  public function prepareView(): void
  {
    parent::prepareView();
    $this->setView('@Hubleto:App:Community:ChartOfAccounts/AccountSubtypes.twig');
  }

}
