<?php

namespace Hubleto\App\Community\Invoices\Controllers\Api;

use Exception;
use Hubleto\App\Community\Customers\Models\Customer;
use Hubleto\App\Community\Invoices\Models\Invoice;
use Hubleto\App\Community\Invoices\Models\Item as InvoiceItem;
use Hubleto\App\Community\Orders\Models\Item;
use Hubleto\App\Community\Orders\Models\Order;

class GetCustomerInfo extends \Hubleto\Erp\Controllers\ApiController
{
  public function renderJson(): array
  {
    $idCustomer = $this->router()->urlParamAsInteger("idCustomer");

    /** @var Customer */
    $mCustomer = $this->getModel(Customer::class);

    return $mCustomer->record->where('id', $idCustomer)->first()?->toArray();
  }

}
