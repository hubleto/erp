<?php

namespace Hubleto\App\Community\Orders\Controllers\Api;

use Hubleto\App\Community\Orders\Models\Item as OrderItem;
use Hubleto\App\Community\Invoices\Models\Item as InvoiceItem;

class PrepareItemsForInvoice extends \Hubleto\Erp\Controllers\ApiController
{
  public function renderJson(): array
  {
    $idOrder = $this->router()->urlParamAsInteger("idOrder");
    $idItems = $this->router()->urlParamAsArray("idItems");

    $idInvoiceItem = 0;

    /** @var Item */
    $mOrderItem = $this->getModel(OrderItem::class);

    /** @var InvoiceItem */
    $mInvoiceItem = $this->getModel(InvoiceItem::class);

    $invoiceItemsCreated = 0;

    foreach ($idItems as $idItem) {
      $idItem = (int) $idItem;
      if ($idItem <= 0) continue;

      $orderItem = $mOrderItem->record
        ->where($mOrderItem->table . '.id', $idItem)
        ->where($mOrderItem->table . '.id_order', $idOrder)
        ->whereNull($mOrderItem->table . '.id_invoice_item')
        ->with('ORDER')
        ->first();

      if ($orderItem) {
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
          'attachment_1' => $orderItem['attachment_1'],
          'attachment_2' => $orderItem['attachment_2'],
        ])['id'];

        $mOrderItem->record
          ->where($mOrderItem->table . '.id', $idItem)
          ->where($mOrderItem->table . '.id_order', $idOrder)
          ->update(['id_invoice_item' => $idInvoiceItem])
        ;

        $invoiceItemsCreated++;
      }
    }

    return [
      "status" => "success",
      "invoiceItemsCreated" => $invoiceItemsCreated,
    ];
  }

}
