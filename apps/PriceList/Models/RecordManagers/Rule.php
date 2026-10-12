<?php

namespace Hubleto\App\Community\PriceList\Models\RecordManagers;

use Hubleto\App\Community\Customers\Models\RecordManagers\Customer;
use Hubleto\App\Community\Customers\Models\RecordManagers\Tag;
use Hubleto\App\Community\Products\Models\RecordManagers\Category;
use Hubleto\App\Community\Products\Models\RecordManagers\Group;
use Hubleto\App\Community\Products\Models\RecordManagers\Product;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Rule extends \Hubleto\Erp\RecordManager
{
  public $table = 'price_lists_rules';

  /** @return BelongsTo<PriceList, covariant Rule> */
  public function PRICE_LIST(): BelongsTo
  {
    return $this->belongsTo(PriceList::class, 'id_price_list', 'id');
  }

  /** @return BelongsTo<Product, covariant Rule> */
  public function PRODUCT(): BelongsTo
  {
    return $this->belongsTo(Product::class, 'id_product', 'id');
  }

  /** @return BelongsTo<Category, covariant Rule> */
  public function CATEGORY(): BelongsTo
  {
    return $this->belongsTo(Category::class, 'id_category', 'id');
  }

  /** @return BelongsTo<Group, covariant Rule> */
  public function GROUP(): BelongsTo
  {
    return $this->belongsTo(Group::class, 'id_group', 'id');
  }

  /** @return BelongsTo<Customer, covariant Rule> */
  public function CUSTOMER(): BelongsTo
  {
    return $this->belongsTo(Customer::class, 'id_customer', 'id');
  }

  /** @return BelongsTo<Tag, covariant Rule> */
  public function CUSTOMER_TAG(): BelongsTo
  {
    return $this->belongsTo(Tag::class, 'id_customer_tag', 'id');
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

    $valuesfKind = array_map('intval', (array) ($filters['fKind'] ?? []));
    if (!empty($valuesfKind)) $query = $query->whereIn($this->table . '.kind', $valuesfKind);

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
