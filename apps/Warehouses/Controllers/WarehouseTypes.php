<?php

namespace HubletoApp\Community\Warehouses\Controllers;

class WarehouseTypes extends \HubletoMain\Controller
{
  public function getBreadcrumbs(): array
  {
    return array_merge(parent::getBreadcrumbs(), [
      [ 'url' => 'warehouses', 'content' => 'Warehouses' ],
      [ 'url' => 'settings', 'content' => 'Settings' ],
      [ 'url' => 'warehouse-types', 'content' => 'Warehouse types' ],
    ]);
  }

  public function prepareView(): void
  {
    parent::prepareView();
    $this->setView('@HubletoApp:Community:Warehouses/WarehouseTypes.twig');
  }

}
