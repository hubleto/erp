<?php

namespace Hubleto\App\Community\Cashdesk\Controllers;

class Home extends \Hubleto\Erp\Controller
{
  public function getBreadcrumbs(): array
  {
    return array_merge(parent::getBreadcrumbs(), [
      [ 'url' => 'cashdesk', 'content' => $this->translate('Cashdesk') ],
    ]);
  }

  public function prepareView(): void
  {
    parent::prepareView();
    $this->setView('@Hubleto:App:Community:Cashdesk/Home.twig');
  }

}
