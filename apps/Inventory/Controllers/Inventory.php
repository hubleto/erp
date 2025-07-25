<?php

namespace HubletoApp\Community\Inventory\Controllers;

class Inventory extends \HubletoMain\Controller
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
    $this->setView('@HubletoApp:Community:Inventory/Inventory.twig');
  }

}
