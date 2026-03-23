<?php

namespace Hubleto\App\Community\Warehouses\Controllers;

class WarehouseTypes extends \Hubleto\Erp\Controller
{
  public function getBreadcrumbs(): array
  {
    return array_merge(parent::getBreadcrumbs(), [
      [ 'url' => 'warehouses', 'content' => $this->translate('Warehouses') ],
      [ 'url' => 'settings', 'content' => $this->translate('Settings') ],
      [ 'url' => 'warehouse-types', 'content' => $this->translate('Warehouse types') ],
    ]);
  }

  public function prepareView(): void
  {
    parent::prepareView();
    $this->setView('@Hubleto:App:Community:Warehouses/WarehouseTypes.twig');
  }

}
