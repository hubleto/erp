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
   * [Description for queryForOpenOrdersWithoutFuturePlan]
   *
   * @return mixed
   * 
   */
  public function queryForOpenOrdersWithoutFuturePlan(): mixed
  {
    $mOrder = $this->getModel(Models\Order::class);

    return $mOrder->record->prepareReadQuery()
      ->whereDoesntHave('ACTIVITIES', function($q) {
        $q->where('completed', false);
        $q->whereDate('date_start', '>=', date("Y-m-d"));
      })
      ->where($mOrder->table . '.is_closed', false)
    ;

  }

  /**
   * [Description for openOrdersWithoutFuturePlan]
   *
   * @return int
   * 
   */
  public function openOrdersWithoutFuturePlan(): int
  {
    return $this->queryForOpenOrdersWithoutFuturePlan()->count();
  }

  /**
   * [Description for queryForOrdersAwaitingInvoice]
   *
   * @return mixed
   * 
   */
  public function queryForOrdersAwaitingInvoice(): mixed
  {
    $mOrder = $this->getModel(Models\Order::class);

    return $mOrder->record
      ->prepareReadQuery()
      ->selectRaw('
        orders.id,
        ifnull(max(orders_items.charged_period_end), "2000-01-01") as last_charged_period_end
      ')
      ->leftJoin('orders_items', 'orders_items.id_order', '=', 'orders.id')
      ->groupBy('orders.id')
      ->whereRaw('
        ifnull(orders.is_closed, 0) = 0
        and ifnull(orders.date_next_invoice_expected, "2100-01-01") <= now()
      ')
      ->pluck('id')
    ;
  }

  /**
   * [Description for ordersAwaitingInvoice]
   *
   * @return int
   * 
   */
  public function ordersAwaitingInvoice(): int
  {
    return $this->queryForOrdersAwaitingInvoice()->count();
  }

}
