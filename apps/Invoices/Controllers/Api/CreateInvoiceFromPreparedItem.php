<?php declare(strict_types=1);

namespace Hubleto\App\Community\Invoices\Controllers\Api;

use Hubleto\App\Community\Invoices\Models\Invoice;
use Hubleto\App\Community\Invoices\Models\Item;

class CreateInvoiceFromPreparedItem extends \Hubleto\Erp\Controllers\ApiController
{
  public function renderJson(): array
  {
    $idItem = $this->router()->urlParamAsInteger('idItem');

    /** @var Invoice */
    $mInvoice = $this->getModel(Invoice::class);

    /** @var Item */
    $mItem = $this->getModel(Item::class);

    $item = $mItem->record
      ->whereNull('id_invoice')
      ->where('id', $idItem)
      ->first();

    if ($item) {

      $idItem = $item->id;

      $idInvoice = $mInvoice->record->recordCreate([
        'inbound_outbound' => Invoice::OUTBOUND_INVOICE,
        'type' => Invoice::TYPE_STANDARD,
        'id_issued_by' => $this->authProvider()->getUserId(),
        'id_customer' => $item->id_customer,
      ])['id'];

      $item = $mItem->record
        ->whereNull('id_invoice')
        ->where('id', $idItem)
        ->update(['id_invoice' => $idInvoice]);

      return [
        'status' => 'success',
        'idInvoice' => $idInvoice,
        'idItem' => $idItem,
      ];
    } else {
      return [
        'status' => 'error',
        'message' => $this->translate('The prepared item was not found or has already been linked to an invoice.'),
      ];
    }
  }
}
