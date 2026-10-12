<?php

namespace Hubleto\App\Community\PriceLists\Models\RecordManagers;

use Hubleto\App\Community\Auth\Models\RecordManagers\User;
use Hubleto\App\Community\Settings\Models\RecordManagers\Currency;
use Hubleto\App\Community\Workflow\Models\RecordManagers\Workflow;
use Hubleto\App\Community\Workflow\Models\RecordManagers\WorkflowStep;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PriceList extends \Hubleto\Erp\RecordManager
{
  public $table = 'price_lists';

  /** @return BelongsTo<Currency, covariant PriceList> */
  public function CURRENCY(): BelongsTo
  {
    return $this->belongsTo(Currency::class, 'id_currency', 'id');
  }

  /** @return BelongsTo<PriceList, covariant PriceList> */
  public function PARENT(): BelongsTo
  {
    return $this->belongsTo(PriceList::class, 'id_parent_price_list', 'id');
  }

  /** @return BelongsTo<Workflow, covariant PriceList> */
  public function WORKFLOW(): BelongsTo
  {
    return $this->belongsTo(Workflow::class, 'id_workflow', 'id');
  }

  /** @return BelongsTo<WorkflowStep, covariant PriceList> */
  public function WORKFLOW_STEP(): BelongsTo
  {
    return $this->belongsTo(WorkflowStep::class, 'id_workflow_step', 'id');
  }

  /** @return BelongsTo<User, covariant PriceList> */
  public function OWNER(): BelongsTo
  {
    return $this->belongsTo(User::class, 'id_owner', 'id');
  }

  /** @return BelongsTo<User, covariant PriceList> */
  public function MANAGER(): BelongsTo
  {
    return $this->belongsTo(User::class, 'id_manager', 'id');
  }

  /** @return HasMany<Item, covariant PriceList> */
  public function ITEMS(): HasMany
  {
    return $this->hasMany(Item::class, 'id_price_list', 'id');
  }

  /** @return HasMany<Assignment, covariant PriceList> */
  public function ASSIGNMENTS(): HasMany
  {
    return $this->hasMany(Assignment::class, 'id_price_list', 'id');
  }

  /** @return HasMany<Rule, covariant PriceList> */
  public function RULES(): HasMany
  {
    return $this->hasMany(Rule::class, 'id_price_list', 'id');
  }

  /** @return HasMany<History, covariant PriceList> */
  public function HISTORY(): HasMany
  {
    return $this->hasMany(History::class, 'id_price_list', 'id');
  }

  /** @return HasMany<PriceListHasDocument, covariant PriceList> */
  public function DOCUMENTS(): HasMany
  {
    return $this->hasMany(PriceListHasDocument::class, 'id_price_list', 'id');
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

    $valuesfType = array_map('intval', (array) ($filters['fType'] ?? []));
    if (!empty($valuesfType)) $query = $query->whereIn($this->table . '.type', $valuesfType);

    $valuesfStatus = array_map('intval', (array) ($filters['fStatus'] ?? []));
    if (!empty($valuesfStatus)) $query = $query->whereIn($this->table . '.status', $valuesfStatus);

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
