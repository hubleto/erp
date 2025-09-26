<?php

namespace Hubleto\App\Community\BkAccounts\Controllers;

class Accounts extends \Hubleto\Erp\Controller
{
  public function getBreadcrumbs(): array
  {
    return array_merge(parent::getBreadcrumbs(), [
    ]);
  }

  public function prepareView(): void
  {
    parent::prepareView();

    $this->setView('@Hubleto:App:Community:Accounts/accounts.twig');
  }

}
