<?php

namespace Hubleto\App\Custom\LinkedinMessages\Models\RecordManagers;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Tag extends \Hubleto\Erp\RecordManager
{
  public $table = 'linkedin_tags';

  /** @return HasMany<MessageTag, covariant self> */
  public function MESSAGE_TAGS(): HasMany
  {
    return $this->hasMany(MessageTag::class, 'id_tag', 'id');
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
