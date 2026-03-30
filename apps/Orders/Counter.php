<?php

namespace Hubleto\App\Community\Orders;

use Hubleto\Erp\Core;

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

    $lastDayOfPreviousMonth = date("Y-m-t", strtotime("-2 month"));

    return $mOrder->record
      ->selectRaw('
        orders.id,
        ifnull(max(orders_items.date_due), "2000-01-01") as last_item_date_due
      ')
      ->leftJoin('orders_items', 'orders_items.id_order', '=', 'orders.id')
      ->groupBy('orders.id')
      ->whereRaw('orders.payment_period > 0')
      ->havingRaw('last_item_date_due <= "' . $lastDayOfPreviousMonth . '"')
      ->pluck('id')
      ?->toArray()
    ;
  }

}
