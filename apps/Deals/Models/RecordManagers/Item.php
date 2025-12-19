<?php

namespace Hubleto\App\Community\Deals\Models\RecordManagers;

use Hubleto\App\Community\Products\Models\RecordManagers\Product;
use Hubleto\App\Community\Deals\Models\RecordManagers\Deal;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Item extends \Hubleto\Erp\RecordManager
{
  public $table = 'deal_items';

  /** @return BelongsTo<Deal, covariant Item> */
  public function DEAL(): BelongsTo
  {
    return $this->belongsTo(Deal::class, 'id_deal', 'id');
  }

  /** @return BelongsTo<Product, covariant Item> */
  public function PRODUCT(): BelongsTo
  {
    return $this->belongsTo(Product::class, 'id_product', 'id');
  }

  public function prepareReadQuery(mixed $query = null, int $level = 0): mixed
  {
    $query = parent::prepareReadQuery($query, $level);

    $hubleto = \Hubleto\Erp\Loader::getGlobalApp();

    if ($hubleto->router()->urlParamAsInteger("idDeal") > 0) {
      $query = $query->where("id_deal", $hubleto->router()->urlParamAsInteger("idDeal"));
    }

    return $query;
  }
}
