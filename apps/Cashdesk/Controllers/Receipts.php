<?php

namespace Hubleto\App\Community\Cashdesk\Controllers;

class Receipts extends \Hubleto\Erp\Controller
{
  public function getBreadcrumbs(): array
  {
    return array_merge(parent::getBreadcrumbs(), [
      [ 'url' => 'cashdesk', 'content' => $this->translate('Cashdesk') ],
      [ 'url' => 'receipts', 'content' => $this->translate('Receipts') ],
    ]);
  }

  public function prepareView(): void
  {
    parent::prepareView();
    $this->setView('@Hubleto:App:Community:Cashdesk/Receipts.twig');
  }

}
