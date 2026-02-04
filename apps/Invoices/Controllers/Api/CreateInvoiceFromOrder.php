<?php

namespace Hubleto\App\Community\Invoices\Controllers\Api;

use Exception;
use Hubleto\App\Community\Invoices\Models\Invoice;
use Hubleto\App\Community\Invoices\Models\Item as InvoiceItem;
use Hubleto\App\Community\Orders\Models\Item;
use Hubleto\App\Community\Orders\Models\Order;

class CreateInvoiceFromOrder extends \Hubleto\Erp\Controllers\ApiController
{
  public function renderJson(): array
  {
    $idOrder = $this->router()->urlParamAsInteger("idOrder");

    if ($idOrder <= 0) {
      throw new Exception("The order for converting was not set");
    }

    /** @var Order */
    $mOrder = $this->getModel(Order::class);

    /** @var Item */
    $mOrderItem = $this->getModel(Item::class);

    /** @var Invoice */
    $mInvoice = $this->getModel(Invoice::class);

    /** @var InvoiceItem */
    $mInvoiceItem = $this->getModel(InvoiceItem::class);
    try {

      $order = $mOrder->record->prepareReadQuery()->where($mOrder->table . ".id", $idOrder)->with("ITEMS")->first();
      if (!$order) {
        throw new Exception("Order was not found.");
      }

      $idInvoice = $mInvoice->record->recordCreate([
        'inbound_outbound' => $order->purchase_sales,
        'id_issued_by' => $this->getService(\Hubleto\Framework\AuthProvider::class)->getUserId() ,
        'id_customer' => $order->id_customer ,
        'id_supplier' => $order->id_supplier ,
        'type' => Invoice::TYPE_STANDARD ,
        'number' => $order->identifier ,
        'number_external' => $order->identifier_external,
        //Most of the data bellow is created/passed in onBeforeCreate in the Invoice model using the invoice profile
        //'vs' => $order->identifier_external ,
        //'cs' => $order-> ,
        //'ss' => $order-> ,
        //'date_issue' => date("Y-m-d") ,
        //'date_delivery' => date("Y-m-d") ,
        //'date_due' => strtotime(date("Y-m-d") . " + 14 days") ,// TODO: this value should be choosable
        //'date_payment' => $order-> ,
        //'date_sent' => $order-> ,
        //'id_currency' => $order->id_currency ,
        'total_excl_vat' => $order->price_excl_vat ,
        'total_incl_vat' => $order->price_incl_vat ,
        //'total_payments' => $order-> ,
        'notes' => $order->note ,
      ])["id"];

      foreach ($order->ITEMS as $item) {
        $idInvoiceItem = $mInvoiceItem->record->recordCreate([
          'id_invoice' => $idInvoice,
          'id_customer' => $order->id_customer,
          'id_order' => $order->id,
          'id_order_item' => $item->id,
          'item' => $item->title,
          'unit_price' => $item->unit_price,
          'amount' => $item->amount,
          'discount' => $item->discount,
          'vat' => $item->vat ,
          // 'price_excl_vat' => $item->price_excl_vat ,
          // 'price_vat' => $item->price_vat ,
          // 'price_incl_vat' => $item->price_incl_vat ,
        ])["id"];
        $item->id_invoice_item = $idInvoiceItem;
        $item->save();
      }

    } catch (Exception $e) {
      return [
        "status" => "failed",
        "error" => $e
      ];
    }

    return [
      "status" => "success",
      "idInvoice" => $idInvoice,
      //"title" => str_replace(" ", "+", (string) $project['title'])
    ];
  }

}
