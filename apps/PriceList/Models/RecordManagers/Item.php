<?php

namespace Hubleto\App\Community\PriceList\Models\RecordManagers;

use Hubleto\App\Community\Products\Models\RecordManagers\Category;
use Hubleto\App\Community\Products\Models\RecordManagers\Group;
use Hubleto\App\Community\Products\Models\RecordManagers\Product;
use Hubleto\App\Community\Products\Models\RecordManagers\Unit;
use Hubleto\App\Community\Suppliers\Models\RecordManagers\Supplier;
use Hubleto\App\Community\Settings\Models\RecordManagers\VatRate;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Item extends \Hubleto\Erp\RecordManager
{
  public $table = 'price_lists_items';

  /** @return BelongsTo<PriceList, covariant Item> */
  public function PRICE_LIST(): BelongsTo
  {
    return $this->belongsTo(PriceList::class, 'id_price_list', 'id');
  }

  /** @return BelongsTo<Product, covariant Item> */
  public function PRODUCT(): BelongsTo
  {
    return $this->belongsTo(Product::class, 'id_product', 'id');
  }

  /** @return BelongsTo<Category, covariant Item> */
  public function CATEGORY(): BelongsTo
  {
    return $this->belongsTo(Category::class, 'id_category', 'id');
  }

  /** @return BelongsTo<Group, covariant Item> */
  public function GROUP(): BelongsTo
  {
    return $this->belongsTo(Group::class, 'id_group', 'id');
  }

  /** @return BelongsTo<Unit, covariant Item> */
  public function UNIT(): BelongsTo
  {
    return $this->belongsTo(Unit::class, 'id_unit', 'id');
  }

  /** @return BelongsTo<Supplier, covariant Item> */
  public function SUPPLIER(): BelongsTo
  {
    return $this->belongsTo(Supplier::class, 'id_supplier', 'id');
  }

  /** @return BelongsTo<VatRate, covariant Item> */
  public function VAT_RATE(): BelongsTo
  {
    return $this->belongsTo(VatRate::class, 'id_vat_rate', 'id');
  }

  /** @return BelongsTo<PriceList, covariant Item> */
  public function BASE_PRICE_LIST(): BelongsTo
  {
    return $this->belongsTo(PriceList::class, 'id_base_price_list', 'id');
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

    $valuesfActive = (array) ($filters['fActive'] ?? []);
    if (count($valuesfActive) === 1) $query = $query->where($this->table . '.is_active', (int) $valuesfActive[0] === 1 ? 1 : 0);

    $valuesfValidNow = (array) ($filters['fValidNow'] ?? []);
    if (count($valuesfValidNow) === 1) {
      $table = $this->table;
      $today = date('Y-m-d');
      if ((int) $valuesfValidNow[0] === 1) {
        $query = $query
          ->where(function ($q) use ($table, $today) { $q->whereNull($table . '.valid_from')->orWhere($table . '.valid_from', '<=', $today); })
          ->where(function ($q) use ($table, $today) { $q->whereNull($table . '.valid_to')->orWhere($table . '.valid_to', '>=', $today); })
        ;
      } else {
        $query = $query->where(function ($q) use ($table, $today) {
          $q->where($table . '.valid_from', '>', $today)->orWhere($table . '.valid_to', '<', $today);
        });
      }
    }

    $valuesfMethod = array_map('intval', (array) ($filters['fMethod'] ?? []));
    if (!empty($valuesfMethod)) $query = $query->whereIn($this->table . '.method', $valuesfMethod);

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
