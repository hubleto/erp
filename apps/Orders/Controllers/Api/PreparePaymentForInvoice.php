<?php

namespace Hubleto\App\Community\Orders\Controllers\Api;

use Hubleto\App\Community\Orders\Models\Payment;
use Hubleto\App\Community\Invoices\Models\Item;

class PreparePaymentForInvoice extends \Hubleto\Erp\Controllers\ApiController
{
  public function renderJson(): array
  {
    $idOrder = $this->router()->urlParamAsInteger("idOrder");
    $idPayment = $this->router()->urlParamAsInteger("idPayment");

    /** @var Payment */
    $mPayment = $this->getModel(Payment::class);
    $payment = $mPayment->record
      ->where($mPayment->table . '.id', $idPayment)
      ->where($mPayment->table . '.id_order', $idOrder)
      ->with('ORDER')
      ->first();

    if ($payment) {
      /** @var Item */
      $mItem = $this->getModel(Item::class);
      $idItem = $mItem->record->recordCreate([
        'id_invoice' => 0,
        'id_customer' => $payment->ORDER->id_customer,
        'id_order' => $idOrder,
        'id_order_product' => 0,
        'item' => $payment['title'],
        'unit_price' => $payment['unit_price'],
        'amount' => $payment['amount'],
        'discount' => $payment['discount'],
        'vat' => $payment['vat'],
      ])['id'];

      $mPayment->record
        ->where($mPayment->table . '.id', $idPayment)
        ->where($mPayment->table . '.id_order', $idOrder)
        ->update(['id_invoice_item' => $idItem])
      ;

    }

    return [
      "status" => "success",
    ];
  }

}
