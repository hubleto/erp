<?php

namespace Hubleto\App\Community\Cashdesk\Controllers;

class CashRegisters extends \Hubleto\Erp\Controller
{
  public function getBreadcrumbs(): array
  {
    return array_merge(parent::getBreadcrumbs(), [
      // [ 'url' => 'cashdesk', 'content' => $this->translate('Cashdesk') ],
      [ 'url' => 'cashdesk/cash-registers', 'content' => $this->translate('Cash registers') ],
    ]);
  }

  public function prepareView(): void
  {
    parent::prepareView();
    $this->setView('@Hubleto:App:Community:Cashdesk/CashRegisters.twig');
  }

}
