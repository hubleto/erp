<?php

namespace Hubleto\App\Community\Inventory\Controllers;

class Inventory extends \Hubleto\Erp\Controller
{
  public function getBreadcrumbs(): array
  {
    return array_merge(parent::getBreadcrumbs(), [
      [ 'url' => 'inventory', 'content' => 'Inventory' ],
    ]);
  }

  public function prepareView(): void
  {
    parent::prepareView();
    $this->setView('@Hubleto:App:Community:Inventory/Inventory.twig');
  }

}
