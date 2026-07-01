<?php

namespace Hubleto\App\Community\Orders;

use Hubleto\Erp\Core;

class Counter extends Core
{

  /**
   * [Description for queryForDueAndChargeableItemsNotPreparedForInvoice]
   *
   * @return mixed
   * 
   */
  public function queryForDueAndChargeableItemsNotPreparedForInvoice(): mixed
  {
    $mItem = $this->getModel(Models\Item::class);
    return $mItem->record->prepareReadQuery()
      ->whereDate('orders_items.date_due', '<', date("Y-m-d"))
      ->whereNull('orders_items.id_invoice_item')
      ->where('orders_items.is_chargeable', 1)
      ->where('orders_items.price_excl_vat', '>', 0)
    ;
  }

  /**
   * [Description for dueAndChargeableItemsNotPreparedForInvoice]
   *
   * @return int
   * 
   */
  public function dueAndChargeableItemsNotPreparedForInvoice(): int
  {
    return $this->queryForDueAndChargeableItemsNotPreparedForInvoice()->count();
  }

  /**
   * [Description for periodicalOrdersMissingItems]
   *
   * @return array
   * 
   */
  public function periodicalOrdersMissingItems(): array
  {
    $mOrder = $this->getModel(Models\Order::class);

    $lastDayOfPreviousMonth = date("Y-m-t", strtotime("-1 month"));

    return $mOrder->record
      ->prepareReadQuery()
      ->selectRaw('
        orders.id,
        ifnull(max(orders_items.charged_period_end), "2000-01-01") as last_charged_period_end
      ')
      ->leftJoin('orders_items', 'orders_items.id_order', '=', 'orders.id')
      ->groupBy('orders.id')
      ->whereRaw('orders.payment_period > 0 and ifnull(orders.is_closed, 0) = 0')
      ->havingRaw('last_charged_period_end <= "' . $lastDayOfPreviousMonth . '"')
      ->pluck('id')
      ?->toArray()
    ;
  }

}
