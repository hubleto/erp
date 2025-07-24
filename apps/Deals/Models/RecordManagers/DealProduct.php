<?php

namespace HubletoApp\Community\Deals\Models\RecordManagers;

use HubletoApp\Community\Products\Models\RecordManagers\Product;
use HubletoApp\Community\Deals\Models\RecordManagers\Deal;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DealProduct extends \Hubleto\Framework\RecordManager
{
  public $table = 'deal_products';

  /** @return BelongsTo<Deal, covariant DealProduct> */
  public function DEAL(): BelongsTo
  {
    return $this->belongsTo(Deal::class, 'id_deal', 'id');
  }

  /** @return BelongsTo<Product, covariant DealProduct> */
  public function PRODUCT(): BelongsTo
  {
    return $this->belongsTo(Product::class, 'id_product', 'id');
  }

}
