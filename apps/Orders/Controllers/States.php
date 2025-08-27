<?php

namespace Hubleto\App\Community\Orders\Controllers;

class States extends \Hubleto\Erp\Controller
{
  public function getBreadcrumbs(): array
  {
    return array_merge(parent::getBreadcrumbs(), [
      [ 'url' => 'orders', 'content' => $this->translate('Orders') ],
      [ 'url' => '', 'content' => $this->translate('States') ],
    ]);
  }

  public function prepareView(): void
  {
    parent::prepareView();

    $this->setView('@Hubleto:App:Community:Orders/States.twig');

  }

}
