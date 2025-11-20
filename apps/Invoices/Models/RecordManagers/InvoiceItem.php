<?php

namespace Hubleto\App\Community\Invoices\Models\RecordManagers;

use Hubleto\App\Community\Orders\Models\RecordManagers\Order;
use Hubleto\App\Community\Orders\Models\RecordManagers\OrderProduct;
use \Illuminate\Database\Eloquent\Relations\BelongsTo;

class InvoiceItem extends \Hubleto\Erp\RecordManager {
  public $table = 'invoice_items';

  /** @return BelongsTo<Invoice, covariant InvoiceItem> */
  public function INVOICE(): BelongsTo {
    return $this->BelongsTo(Invoice::class, 'id_invoice');
  }

  /** @return BelongsTo<Invoice, covariant InvoiceItem> */
  public function ORDER(): BelongsTo {
    return $this->BelongsTo(Order::class, 'id_order');
  }

  /** @return BelongsTo<Invoice, covariant InvoiceItem> */
  public function ORDER_PRODUCT(): BelongsTo {
    return $this->BelongsTo(OrderProduct::class, 'id_order_product');
  }

}
