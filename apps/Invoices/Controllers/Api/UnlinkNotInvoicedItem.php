<?php declare(strict_types=1);

namespace Hubleto\App\Community\Invoices\Controllers\Api;

use Hubleto\App\Community\Invoices\Models\Invoice;
use Hubleto\App\Community\Invoices\Models\Item;

class UnlinkNotInvoicedItem extends \Hubleto\Erp\Controllers\ApiController
{
  public function renderJson(): array
  {
    $idInvoice = $this->router()->urlParamAsInteger('idInvoice');
    $idItem = $this->router()->urlParamAsInteger('idItem');

    /** @var Item */
    $mItem = $this->getModel(Item::class);

    $mItem->record
      ->where('id_invoice', $idInvoice)
      ->where('id', $idItem)
      ->update(['id_invoice' => null]);

    return [
      'status' => 'success',
    ];
  }
}
