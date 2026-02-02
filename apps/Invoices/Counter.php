<?php

namespace Hubleto\App\Community\Invoices;

use Hubleto\Framework\Core;

class Counter extends Core
{

  /**
   * [Description for preparedItems]
   *
   * @return int
   * 
   */
  public function preparedItems(): int
  {
    $mItem = $this->getModel(Models\Item::class);
    return $mItem->record->prepareReadQuery()
      ->whereNull('id_invoice')
      ->count()
    ;
  }

  /**
   * [Description for notPaidInvoices]
   *
   * @return int
   * 
   */
  public function notPaidInvoices(): int
  {
    $mItem = $this->getModel(Models\Invoice::class);
    return $mItem->record->prepareReadQuery()
      ->whereNull('date_payment')
      ->count()
    ;
  }

  /**
   * [Description for dueAndNotPaidInvoices]
   *
   * @return int
   * 
   */
  public function dueAndNotPaidInvoices(): int
  {
    $mItem = $this->getModel(Models\Invoice::class);
    return $mItem->record->prepareReadQuery()
      ->whereDate('date_due', '<', date("Y-m-d"))
      ->whereNull('date_payment')
      ->count()
    ;
  }

  /**
   * [Description for unsentInvoices]
   *
   * @return int
   * 
   */
  public function unsentInvoices(): int
  {
    $mItem = $this->getModel(Models\Invoice::class);
    return $mItem->record->prepareReadQuery()
      ->whereNull('date_sent')
      ->count()
    ;
  }

}
