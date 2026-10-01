<?php

namespace Hubleto\App\Custom\LinkedinMessages\Models\RecordManagers;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MessageActivity extends \Hubleto\App\Community\Calendar\Models\RecordManagers\Activity
{
  public $table = 'linkedin_message_activities';

  /** @return BelongsTo<Message, covariant self> */
  public function MESSAGE(): BelongsTo
  {
    return $this->belongsTo(Message::class, 'id_message', 'id');
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

    if ($hubleto->router()->urlParamAsInteger('idMessage') > 0) {
      $query = $query->where($this->table . '.id_message', $hubleto->router()->urlParamAsInteger('idMessage'));
    }
    $fFollowupCompleted = (int) ($filters['fFollowupCompleted'] ?? 1);
    if ($fFollowupCompleted == 1) $query = $query->where($this->table . '.completed', false);
    if ($fFollowupCompleted == 2) $query = $query->where($this->table . '.completed', true);

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
