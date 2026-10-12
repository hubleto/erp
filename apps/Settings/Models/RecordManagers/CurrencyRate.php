<?php

namespace Hubleto\App\Community\Settings\Models\RecordManagers;

use Hubleto\App\Community\Settings\Models\RecordManagers\Currency;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CurrencyRate extends \Hubleto\Erp\RecordManager
{
  public $table = 'currency_rates';

  /** @return BelongsTo<Currency, covariant CurrencyRate> */
  public function CURRENCY(): BelongsTo
  {
    return $this->belongsTo(Currency::class, 'id_currency', 'id');
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
