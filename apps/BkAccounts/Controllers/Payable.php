<?php

namespace Hubleto\App\Community\BkAccounts\Controllers;

class Payable extends \Hubleto\Erp\Controller
{
  public function getBreadcrumbs(): array
  {
    return array_merge(parent::getBreadcrumbs(), [
      [ 'url' => 'accounts/payable', 'content' => $this->translate('Payable') ],
    ]);
  }

  public function prepareView(): void
  {
    parent::prepareView();
    $this->setView('@Hubleto:App:Community:Accounts/payable.twig');
  }

}
