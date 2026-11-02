<?php declare(strict_types=1);

namespace Hubleto\App\Community\Invoices\Controllers\Api;

use Hubleto\App\Community\Invoices\Models\Invoice;
use Hubleto\App\Community\Invoices\Models\Item;

class LinkExistingItem extends \Hubleto\Erp\Controllers\ApiController
{
  public function renderJson(): array
  {
    $idInvoice = $this->router()->urlParamAsInteger('idInvoice');
    $idItem = $this->router()->urlParamAsInteger('idItem');

    /** @var Invoice */
    $mInvoice = $this->getModel(Invoice::class);

    /** @var Item */
    $mItem = $this->getModel(Item::class);

    $mItem->record
      ->whereNull('id_invoice')
      ->where('id', $idItem)
      ->update(['id_invoice' => $idInvoice]);

    $mInvoice->recalculateTotalsForInvoice($idInvoice);

    return [
      'status' => 'success',
    ];
  }
}
