<?php

namespace Hubleto\App\Community\Invoices\Models\RecordManagers;

use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Payment extends \Hubleto\Erp\RecordManager
{
  public $table = 'invoice_payments';

  /** @return BelongsTo<User, covariant User> */
  public function INVOICE(): BelongsTo
  {
    return $this->belongsTo(Invoice::class, 'id_invoice', 'id');
  }

  public function prepareReadQuery(mixed $query = null, int $level = 0): mixed
  {
    $query = parent::prepareReadQuery($query, $level);

    $hubleto = \Hubleto\Erp\Loader::getGlobalApp();
    $idInvoice = $hubleto->router()->urlParamAsInteger("idInvoice");

    if ($idInvoice > 0) $query = $query->where($this->table . '.id_invoice', $idInvoice);

    return $query;
  }

}
