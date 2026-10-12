<?php

namespace Hubleto\App\Community\PriceLists\Models\RecordManagers;

use Hubleto\App\Community\Products\Models\RecordManagers\Product;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Resolution extends \Hubleto\Erp\RecordManager
{
  public $table = 'price_lists_resolutions';

  /** @return BelongsTo<PriceList, covariant Resolution> */
  public function PRICE_LIST(): BelongsTo
  {
    return $this->belongsTo(PriceList::class, 'id_price_list', 'id');
  }

  /** @return BelongsTo<Item, covariant Resolution> */
  public function ITEM(): BelongsTo
  {
    return $this->belongsTo(Item::class, 'id_item', 'id');
  }

  /** @return BelongsTo<Product, covariant Resolution> */
  public function PRODUCT(): BelongsTo
  {
    return $this->belongsTo(Product::class, 'id_product', 'id');
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

    if ($hubleto->router()->urlParamAsInteger('idParentRecord') > 0) {
      $query = $query->where($this->table . '.parent_record_id', $hubleto->router()->urlParamAsInteger('idParentRecord'));
    }
    if (!empty($hubleto->router()->urlParamAsString('parentModel'))) {
      $query = $query->where($this->table . '.parent_model', $hubleto->router()->urlParamAsString('parentModel'));
    }

    $valuesfDeviates = (array) ($filters['fDeviates'] ?? []);
    if (count($valuesfDeviates) === 1) $query = $query->where($this->table . '.deviates', (int) $valuesfDeviates[0] === 1 ? 1 : 0);

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
