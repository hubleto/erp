<?php

namespace Hubleto\App\Community\Accounting\Controllers;

class Receivable extends \Hubleto\Erp\Controller
{
  public function getBreadcrumbs(): array
  {
    return array_merge(parent::getBreadcrumbs(), [
      [ 'url' => 'accounts/receivable', 'content' => $this->translate('Receivable') ],
    ]);
  }

  public function prepareView(): void
  {
    parent::prepareView();
    $this->setView('@Hubleto:App:Community:Accounting/receivable.twig');
  }

}
