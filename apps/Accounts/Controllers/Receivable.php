<?php

namespace Hubleto\App\Community\Accounts\Controllers;

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
    $this->setView('@Hubleto:App:Community:Accounts/receivable.twig');
  }

}
