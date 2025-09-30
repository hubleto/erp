<?php

namespace Hubleto\App\Community\Warehouses;

class StockStatus extends \Hubleto\Framework\Core
{

  public function recalculateCapacityAndStockStatusOfWarehouse(int $idWarehouse): void
  {
    $this->db()->execute("
      update `warehouses` set
        `capacity` = ifnull((select sum(ifnull(`capacity`, 0)) from `warehouses_locations` where `id_warehouse` = :idWarehouse), 0),
        `current_stock_status` = ifnull((select sum(ifnull(`current_stock_status`, 0)) from `warehouses_locations` where `id_warehouse` = :idWarehouse), 0)
      where `id` = :idWarehouse
    ", ["idWarehouse" => $idWarehouse]);
  }

}