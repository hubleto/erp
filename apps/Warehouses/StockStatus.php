<?php

namespace Hubleto\App\Community\Warehouses;

use Hubleto\App\Community\Products\Models\Product;
use Hubleto\App\Community\Warehouses\Models\Location;
use Hubleto\App\Community\Warehouses\Models\Transaction;

class StockStatus extends \Hubleto\Framework\Core
{

  /**
   * [Description for recalculateCapacityAndStockStatusOfWarehouse]
   *
   * @param int $idWarehouse
   * 
   * @return void
   * 
   */
  public function recalculateCapacityAndStockStatusOfWarehouse(int $idWarehouse): void
  {

    // add items to inventory for all locations and all products
    $mProduct = $this->getModel(Product::class);
    $mLocation = $this->getModel(Location::class);

    $productIds = $mProduct->record->pluck('id');
    $locationIds = $mLocation->record->pluck('id');
    foreach ($productIds as $idProduct) {
      foreach ($locationIds as $idLocation) {
        $this->db()->execute('
          insert ignore into `warehouses_inventory` set
            `id_product` = :idProduct,
            `id_location` = :idLocation,
            `quantity` = 0
        ', ['idProduct' => $idProduct, 'idLocation' => $idLocation]);
      }
    }

    // start transaction
    $this->db()->execute('start transaction');

    // recalculate stock status of all locations in the warehouse
    $this->db()->execute('
      update `warehouses_locations` `wl` set
        `current_stock_status` =
          /* add inbound transactions */
          ifnull(
            (
              select sum(ifnull(`quantity`, 0))
              from `warehouses_transactions_items` `wti`
              left join `warehouses_transactions` `wt` on `wt`.id = `wti`.id_transaction
              where
                `wti`.`id_location_new` = `wl`.`id`
                and `wt`.`direction` = ' . Transaction::DIRECTION_INBOUND . '
            ),
            0
          )

          /* subtract outbound transactions */
          - ifnull(
            (
              select sum(ifnull(`quantity`, 0))
              from `warehouses_transactions_items` `wti`
              left join `warehouses_transactions` `wt` on `wt`.id = `wti`.id_transaction
              where
                `wti`.`id_location_new` = `wl`.`id`
                and `wt`.`direction` = ' . Transaction::DIRECTION_OUTBOUND . '
            ),
            0
          )
      where
        `wl`.`id_warehouse` = :idWarehouse
    ', ['idWarehouse' => $idWarehouse]);

    // recalculate inventory
    $this->db()->execute('
      update `warehouses_inventory` `wi` set
        `quantity` =
          /* add inbound transactions */
          ifnull(
            (
              select sum(ifnull(`quantity`, 0))
              from `warehouses_transactions_items` `wti`
              left join `warehouses_transactions` `wt` on `wt`.id = `wti`.id_transaction
              where
                `wti`.`id_location_new` = `wi`.`id_location`
                and `wti`.`id_product` = `wi`.`id_product`
                and `wt`.`direction` = ' . Transaction::DIRECTION_INBOUND . '
            ),
            0
          )

          /* subtract outbound transactions */
          - ifnull(
            (
              select sum(ifnull(`quantity`, 0))
              from `warehouses_transactions_items` `wti`
              left join `warehouses_transactions` `wt` on `wt`.id = `wti`.id_transaction
              where
                `wti`.`id_location_new` = `wi`.`id_location`
                and `wti`.`id_product` = `wi`.`id_product`
                and `wt`.`direction` = ' . Transaction::DIRECTION_OUTBOUND . '
            ),
            0
          )
    ');

    // recalculate capacity stock status of warehouse
    $this->db()->execute("
      update `warehouses` set
        `capacity` = ifnull((select sum(ifnull(`capacity`, 0)) from `warehouses_locations` where `id_warehouse` = :idWarehouse), 0),
        `current_stock_status` = ifnull((select sum(ifnull(`current_stock_status`, 0)) from `warehouses_locations` where `id_warehouse` = :idWarehouse), 0)
      where `id` = :idWarehouse
    ", ["idWarehouse" => $idWarehouse]);

    // commit
    $this->db()->execute('commit');
  }

}