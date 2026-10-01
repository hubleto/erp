<?php

namespace Hubleto\App\Custom\LinkedinMessages\Models\RecordManagers;

use Hubleto\App\Community\Auth\Models\RecordManagers\User;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Profile extends \Hubleto\Erp\RecordManager
{
  public $table = 'linkedin_profiles';

  /** @return BelongsTo<User, covariant self> */
  public function OWNER(): BelongsTo
  {
    return $this->belongsTo(User::class, 'id_owner', 'id');
  }

  /** @return HasMany<Message, covariant self> */
  public function MESSAGES(): HasMany
  {
    return $this->hasMany(Message::class, 'id_profile', 'id');
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

    $fulltext = $hubleto->router()->urlParamAsString('profileSearch');
    if ($fulltext != '') {
      $query = $query->where(function ($q) use ($fulltext) {
        $q->where($this->table . '.first_name', 'like', '%' . $fulltext . '%')
          ->orWhere($this->table . '.last_name', 'like', '%' . $fulltext . '%')
          ->orWhere($this->table . '.company', 'like', '%' . $fulltext . '%');
      });
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
