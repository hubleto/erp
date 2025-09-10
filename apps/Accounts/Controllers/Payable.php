<?php

namespace Hubleto\App\Community\Accounts\Controllers;

class Payable extends \Hubleto\Erp\Controller
{
  public function getBreadcrumbs(): array
  {
    return array_merge(parent::getBreadcrumbs(), [
      [ 'url' => 'payable', 'content' => $this->translate('Payable') ],
    ]);
  }

  public function prepareView(): void
  {
    parent::prepareView();
    $this->setView('@Hubleto:App:Community:Accounts/payable.twig');
  }

}
