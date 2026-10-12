<?php

namespace Hubleto\App\Community\Settings\Models\RecordManagers;

use Hubleto\App\Community\Settings\Models\RecordManagers\Country;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class VatRate extends \Hubleto\Erp\RecordManager
{
  public $table = 'vat_rates';

  /** @return BelongsTo<Country, covariant VatRate> */
  public function COUNTRY(): BelongsTo
  {
    return $this->belongsTo(Country::class, 'id_country', 'id');
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

    $valuesfType = array_map('intval', (array) ($filters['fType'] ?? []));
    if (!empty($valuesfType)) $query = $query->whereIn($this->table . '.type', $valuesfType);

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
