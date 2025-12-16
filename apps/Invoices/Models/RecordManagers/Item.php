<?php

namespace Hubleto\App\Community\Invoices\Models\RecordManagers;

use Hubleto\App\Community\Orders\Models\RecordManagers\Order;
use Hubleto\App\Community\Orders\Models\RecordManagers\OrderProduct;
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
  public function ORDER_PRODUCT(): BelongsTo {
    return $this->BelongsTo(OrderProduct::class, 'id_order_product');
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

    $filters = $hubleto->router()->urlParamAsArray("filters");
    if (isset($filters["fStatus"])) {
      if ($filters["fStatus"] == 1) {
        $query = $query->whereNull("invoice_items.id_invoice");
      } else {
        $query = $query->where("invoice_items.id_invoice", ">", 0);
      }
    }

    return $query;
  }

}
