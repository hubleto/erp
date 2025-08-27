<?php

namespace HubletoApp\Community\Deals\Models\RecordManagers;

use HubletoApp\Community\Products\Models\RecordManagers\Product;
use HubletoApp\Community\Deals\Models\RecordManagers\Deal;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DealProduct extends \HubletoMain\RecordManager
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

    $main = \HubletoMain\Loader::getGlobalApp();

    if ($main->getRouter()->urlParamAsInteger("idDeal") > 0) {
      $query = $query->where("id_deal", $main->getRouter()->urlParamAsInteger("idDeal"));
    }

    return $query;
  }
}
