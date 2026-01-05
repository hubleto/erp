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
   * [Description for prepareSelectsForReadQuery]
   *
   * @param mixed|null $query
   * @param int $level
   * @param array|null|null $includeRelations
   * 
   * @return array
   * 
   */
  public function prepareSelectsForReadQuery(mixed $query = null, int $level = 0, array|null $includeRelations = null): array
  {
    $hubleto = \Hubleto\Erp\Loader::getGlobalApp();
    $filters = $hubleto->router()->urlParamAsArray("filters");
    $selects = parent::prepareSelectsForReadQuery($query, $level, $includeRelations);

    if (isset($filters['fGroupBy']) && is_array($filters['fGroupBy'])) {
      $selects[] = 'sum(invoice_items.price_excl_vat) as total_price_excl_vat';
      $selects[] = 'sum(invoice_items.price_incl_vat) as total_price_incl_vat';
    }

    return $selects;
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
  public function prepareReadQuery(mixed $query = null, int $level = 0, array|null $includeRelations = null): mixed
  {
    $query = parent::prepareReadQuery($query, $level, $includeRelations);

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

    if (isset($filters['fGroupBy'])) {
      $fGroupBy = (array) $filters['fGroupBy'];
      if (in_array('customer', $fGroupBy)) $query = $query->groupBy('id_customer');
      if (in_array('order', $fGroupBy)) $query = $query->groupBy('id_order');
      if (in_array('invoice', $fGroupBy)) $query = $query->groupBy('id_invoice');
    }

    return $query;
  }

}
