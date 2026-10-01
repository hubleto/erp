<?php

namespace Hubleto\App\Community\LinkedinMessages\Models\RecordManagers;

use Hubleto\App\Community\Auth\Models\RecordManagers\User;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Account extends \Hubleto\Erp\RecordManager
{
  public $table = 'linkedin_accounts';

  /** @return BelongsTo<User, covariant self> */
  public function OWNER(): BelongsTo
  {
    return $this->belongsTo(User::class, 'id_owner', 'id');
  }

  /** @return HasMany<Message, covariant self> */
  public function MESSAGES(): HasMany
  {
    return $this->hasMany(Message::class, 'id_account', 'id');
  }

  /**
   * @param mixed|null $query
   * @param int $level
   * @param array|null $includeRelations
   *
   * @return mixed
   */
  public function prepareReadQuery(mixed $query = null, int $level = 0, array|null $includeRelations = null): mixed
  {
    $query = parent::prepareReadQuery($query, $level, $includeRelations);

    return $query;
  }

  /**
   * @param mixed $query
   *
   * @return mixed
   */
  public function addUrlFiltersToQuery(mixed $query): mixed
  {
    $query = parent::addUrlFiltersToQuery($query);

    $hubleto = \Hubleto\Erp\Loader::getGlobalApp();
    $filters = $hubleto->router()->urlParamAsArray('filters');

    if ($hubleto->router()->urlParamAsInteger('idAccount') > 0) {
      $query = $query->where($this->table . '.id', $hubleto->router()->urlParamAsInteger('idAccount'));
    }

    return $query;
  }

  /**
   * @param array $dataRaw
   *
   * @return array
   */
  public function prepareLookupData(array $dataRaw): array
  {
    $data = parent::prepareLookupData($dataRaw);

    return $data;
  }
}
