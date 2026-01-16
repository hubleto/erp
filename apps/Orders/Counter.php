<?php

namespace Hubleto\App\Community\Orders;

use Hubleto\Framework\Core;

class Counter extends Core
{

  /**
   * [Description for duePaymentsNotPreparedForInvoice]
   *
   * @return int
   * 
   */
  public function duePaymentsNotPreparedForInvoice(): int
  {
    $mPayment = $this->getModel(Models\Payment::class);
    return $mPayment->record->prepareReadQuery()
      ->whereDate('date_due', '<', date("Y-m-d"))
      ->whereNull('id_invoice_item')
      ->count()
    ;
  }

}
