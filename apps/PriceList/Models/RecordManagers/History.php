<?php

namespace Hubleto\App\Community\PriceList\Models\RecordManagers;

use Hubleto\App\Community\Auth\Models\RecordManagers\User;
use Hubleto\App\Community\Products\Models\RecordManagers\Product;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class History extends \Hubleto\Erp\RecordManager
{
  public $table = 'price_lists_history';

  /** @return BelongsTo<PriceList, covariant History> */
  public function PRICE_LIST(): BelongsTo
  {
    return $this->belongsTo(PriceList::class, 'id_price_list', 'id');
  }

  /** @return BelongsTo<Item, covariant History> */
  public function ITEM(): BelongsTo
  {
    return $this->belongsTo(Item::class, 'id_item', 'id');
  }

  /** @return BelongsTo<Product, covariant History> */
  public function PRODUCT(): BelongsTo
  {
    return $this->belongsTo(Product::class, 'id_product', 'id');
  }

  /** @return BelongsTo<User, covariant History> */
  public function USER(): BelongsTo
  {
    return $this->belongsTo(User::class, 'id_user', 'id');
  }

  /**
   * Prepares the query for reading records.
   *
   * @param mixed $query
   * @param int $level
   * @param array|null $includeRelations
   *
   * @return mixed
   *
   */
  public function prepareReadQuery(mixed $query = null, int $level = 0, array|null $includeRelations = null): mixed
  {
    $query = parent::prepareReadQuery($query, $level, $includeRelations);
    return $query;
  }

  /**
   * Adds filters given in the URL to the query.
   *
   * @param mixed $query
   *
   * @return mixed
   *
   */
  public function addUrlFiltersToQuery(mixed $query): mixed
  {
    $query = parent::addUrlFiltersToQuery($query);

    $hubleto = \Hubleto\Erp\Loader::getGlobalApp();
    $filters = $hubleto->router()->urlParamAsArray('filters');

    if ($hubleto->router()->urlParamAsInteger('idPriceList') > 0) {
      $query = $query->where($this->table . '.id_price_list', $hubleto->router()->urlParamAsInteger('idPriceList'));
    }

    if ($hubleto->router()->urlParamAsInteger('idProduct') > 0) {
      $query = $query->where($this->table . '.id_product', $hubleto->router()->urlParamAsInteger('idProduct'));
    }

    $valuesfEvent = array_map('intval', (array) ($filters['fEvent'] ?? []));
    if (!empty($valuesfEvent)) $query = $query->whereIn($this->table . '.event_type', $valuesfEvent);

    return $query;
  }

  /**
   * Prepares data for lookup inputs.
   *
   * @param array $dataRaw
   *
   * @return array
   *
   */
  public function prepareLookupData(array $dataRaw): array
  {
    $data = parent::prepareLookupData($dataRaw);
    return $data;
  }

}
