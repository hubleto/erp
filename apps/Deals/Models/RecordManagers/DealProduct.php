<?php

namespace Hubleto\App\Community\Deals\Models\RecordManagers;

use Hubleto\App\Community\Products\Models\RecordManagers\Product;
use Hubleto\App\Community\Deals\Models\RecordManagers\Deal;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DealProduct extends \Hubleto\Erp\RecordManager
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

  public function prepareReadQuery(mixed $query = null, int $level = 0): mixed
  {
    $query = parent::prepareReadQuery($query, $level);

    $main = \Hubleto\Erp\Loader::getGlobalApp();

    if ($main->router()->urlParamAsInteger("idDeal") > 0) {
      $query = $query->where("id_deal", $main->router()->urlParamAsInteger("idDeal"));
    }

    return $query;
  }
}
