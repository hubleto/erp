<?php

namespace Hubleto\App\Community\Invoices\Models\RecordManagers;

use Hubleto\App\Community\Orders\Models\RecordManagers\Order;
use Hubleto\App\Community\Orders\Models\RecordManagers\Item as OrderItem;
use Hubleto\App\Community\Customers\Models\RecordManagers\Customer;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Item extends \Hubleto\Erp\RecordManager {
  public $table = 'invoice_items';

  /** @return BelongsTo<Invoice, covariant Item> */
  public function INVOICE(): BelongsTo {
    return $this->BelongsTo(Invoice::class, 'id_invoice');
  }

  /** @return BelongsTo<Customer, covariant Invoice> */
  public function CUSTOMER(): BelongsTo {
    return $this->BelongsTo(Customer::class, 'id_customer');
  }

  /** @return BelongsTo<Invoice, covariant Item> */
  public function ORDER(): BelongsTo {
    return $this->BelongsTo(Order::class, 'id_order');
  }

  /** @return BelongsTo<Invoice, covariant Item> */
  public function ORDER_ITEM(): BelongsTo {
    return $this->BelongsTo(OrderItem::class, 'id_order_Item');
  }

  /**
   * [Description for prepareReadQuery]
   *
   * @param mixed|null $query
   * @param int $level
   * 
   * @return mixed
   * 
   */
  public function prepareReadQuery(mixed $query = null, int $level = 0): mixed
  {
    $query = parent::prepareReadQuery($query, $level);

    $hubleto = \Hubleto\Erp\Loader::getGlobalApp();

    $idCustomer = $hubleto->router()->urlParamAsInteger('idCustomer');
    if ($idCustomer > 0) $query->where('invoice_items.id_customer', $idCustomer);

    $filters = $hubleto->router()->urlParamAsArray("filters");

    if (isset($filters["fStatus"])) {
      switch ($filters["fStatus"]) {
        case 1: $query = $query->whereNull("invoice_items.id_invoice"); break;
        case 2: $query = $query->where("invoice_items.id_invoice", ">", 0); break;
      }
    }

    if (isset($filters['fPeriod'])) {
      switch ($filters['fPeriod']) {
        case 'today': $query = $query->whereHas('INVOICE', function ($q) { return $q->whereDate('invoices.date_due', date('Y-m-d')); }); break;
        case 'yesterday': $query = $query->whereHas('INVOICE', function ($q) { return $q->whereDate('date_due', date('Y-m-d', strtotime('-1 day'))); }); break;
        case 'last7Days': $query = $query->whereHas('INVOICE', function ($q) { return $q->whereDate('date_due', '>=', date('Y-m-d', strtotime('-7 days'))); }); break;
        case 'last14Days': $query = $query->whereHas('INVOICE', function ($q) { return $q->whereDate('date_due', '>=', date('Y-m-d', strtotime('-14 days'))); }); break;
        case 'thisMonth': $query = $query->whereHas('INVOICE', function ($q) { return $q->whereMonth('date_due', date('m')); }); break;
        case 'lastMonth': $query = $query->whereHas('INVOICE', function ($q) { return $q->whereMonth('date_due', date('m', strtotime('-1 month'))); }); break;
        case 'beforeLastMonth': $query = $query->whereHas('INVOICE', function ($q) { return $q->whereMonth('date_due', date('m', strtotime('-2 month'))); }); break;
        case 'thisYear': $query = $query->whereHas('INVOICE', function ($q) { return $q->whereYear('date_due', date('Y')); }); break;
        case 'lastYear': $query = $query->whereHas('INVOICE', function ($q) { return $q->whereYear('date_due', date('Y') - 1); }); break;
      }
    }

    return $query;
  }

}
