<?php

namespace Hubleto\App\Community\Orders;

use Hubleto\Framework\Core;

class Counter extends Core
{

  /**
   * [Description for dueItemsNotPreparedForInvoice]
   *
   * @return int
   * 
   */
  public function dueItemsNotPreparedForInvoice(): int
  {
    $mItem = $this->getModel(Models\Item::class);
    return $mItem->record->prepareReadQuery()
      ->whereDate('orders_items.date_due', '<', date("Y-m-d"))
      ->whereNull('orders_items.id_invoice_item')
      ->count()
    ;
  }

  public function periodicalOrdersMissingItems(): array
  {
    $mOrder = $this->getModel(Models\Order::class);
        // (
        //   select
        //     count(orders_items.id)
        //   from orders_items
        //   where
        //     orders_items.id_order = orders.id
        //     and orders_items.date_due > date_sub(now(), interval orders.payment_period month)
        // ) as items_inside_payment_period,
    return $mOrder->record
      ->selectRaw('
        orders.id,
        ifnull(max(orders_items.date_due), "2000-01-01") as last_item_date_due
      ')
      ->leftJoin('orders_items', 'orders_items.id_order', '=', 'orders.id')
      // ->whereRaw('orders_items.date_due > date_sub(now(), interval orders.payment_period month)')
      // ->groupBy('id_order')
      ->whereRaw('orders.payment_period > 0')
      // ->havingRaw('items_inside_payment_period <= 0')
      ->havingRaw('last_item_date_due <= now()')
      ->pluck('id')
      ?->toArray()
    ;
  }

}
