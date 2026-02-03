<?php

namespace Hubleto\App\Community\Orders\Controllers\Api;

use Hubleto\App\Community\Orders\Models\Item as OrderItem;
use Hubleto\App\Community\Invoices\Models\Item as InvoiceItem;

class PrepareItemForInvoice extends \Hubleto\Erp\Controllers\ApiController
{
  public function renderJson(): array
  {
    $idOrder = $this->router()->urlParamAsInteger("idOrder");
    $idItem = $this->router()->urlParamAsInteger("idItem");

    /** @var Item */
    $mOrderItem = $this->getModel(OrderItem::class);
    $orderItem = $mOrderItem->record
      ->where($mOrderItem->table . '.id', $idItem)
      ->where($mOrderItem->table . '.id_order', $idOrder)
      ->with('ORDER')
      ->first();

    if ($orderItem) {
      /** @var Item */
      $mInvoiceItem = $this->getModel(InvoiceItem::class);
      $idInvoiceItem = $mInvoiceItem->record->recordCreate([
        'id_invoice' => 0,
        'id_customer' => $orderItem->ORDER->id_customer,
        'id_order' => $idOrder,
        'id_order_item' => $idItem,
        'item' => $orderItem['title'],
        'unit_price' => $orderItem['unit_price'],
        'amount' => $orderItem['amount'],
        'discount' => $orderItem['discount'],
        'vat' => $orderItem['vat'],
      ])['id'];

      $mOrderItem->record
        ->where($mOrderItem->table . '.id', $idItem)
        ->where($mOrderItem->table . '.id_order', $idOrder)
        ->update(['id_invoice_item' => $idInvoiceItem])
      ;

    }

    return [
      "status" => "success",
    ];
  }

}
