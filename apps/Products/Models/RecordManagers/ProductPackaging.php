<?php

namespace Hubleto\App\Community\Products\Models\RecordManagers;

use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductPackaging extends \Hubleto\Erp\RecordManager
{
  public $table = 'product_packagings';

  /** @return BelongsTo */
  public function PRODUCT(): BelongsTo
  {
    return $this->belongsTo(Product::class, 'id_product', 'id');
  }

  /** @return BelongsTo */
  public function UNIT(): BelongsTo
  {
    return $this->belongsTo(Unit::class, 'id_unit', 'id');
  }
}
