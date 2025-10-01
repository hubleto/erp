<?php

namespace Hubleto\App\Community\Warehouses\Controllers;

use Hubleto\App\Community\Warehouses\StockStatus;

class Warehouses extends \Hubleto\Erp\Controller
{
  public function prepareView(): void
  {
    parent::prepareView();

    /** @var StockStatus */
    $stockStatus = $this->getService(StockStatus::class);
    $stockStatus->recalculateCapacityAndStockStatusOfWarehouse(1);

    $this->setView('@Hubleto:App:Community:Warehouses/Warehouses.twig');
  }

}
