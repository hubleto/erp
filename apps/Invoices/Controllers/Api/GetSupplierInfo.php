<?php

namespace Hubleto\App\Community\Invoices\Controllers\Api;

use Hubleto\App\Community\Suppliers\Models\Supplier;

class GetSupplierInfo extends \Hubleto\Erp\Controllers\ApiController
{
  public function renderJson(): array
  {
    $idSupplier = $this->router()->urlParamAsInteger("idSupplier");

    /** @var Supplier */
    $mSupplier = $this->getModel(Supplier::class);

    return $mSupplier->record->where('id', $idSupplier)->first()?->toArray();
  }

}
