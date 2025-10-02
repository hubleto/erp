<?php

namespace Hubleto\App\Community\Cashdesk\Models\RecordManagers;


use Hubleto\App\Community\Products\Models\RecordManagers\Product;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Hubleto\App\Community\Auth\Models\RecordManagers\User;

class ReceiptItem extends \Hubleto\Erp\RecordManager
{
  public $table = 'cashdesk_receipts_items';

  public function RECEIPT(): BelongsTo
  {
    return $this->belongsTo(Receipt::class, 'id_receipt', 'id');
  }

  public function PRODUCT(): BelongsTo
  {
    return $this->belongsTo(Product::class, 'id_product', 'id');
  }

  public function prepareReadQuery(mixed $query = null, int $level = 0): mixed
  {
    $query = parent::prepareReadQuery($query, $level);

    $hubleto = \Hubleto\Erp\Loader::getGlobalApp();
    $idReceipt = $hubleto->router()->urlParamAsInteger("idReceipt");
    $idProduct = $hubleto->router()->urlParamAsInteger("idProduct");

    if ($idReceipt > 0) $query = $query->where($this->table . '.id_receipt', $idReceipt);
    if ($idProduct > 0) $query = $query->where($this->table . '.id_product', $idProduct);

    return $query;
  }

}
