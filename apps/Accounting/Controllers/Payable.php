<?php

namespace Hubleto\App\Community\Accounting\Controllers;

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
    $this->setView('@Hubleto:App:Community:Accounting/payable.twig');
  }

}
