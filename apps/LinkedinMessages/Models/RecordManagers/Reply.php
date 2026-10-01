<?php

namespace Hubleto\App\Community\LinkedinMessages\Models\RecordManagers;

use Hubleto\App\Community\Auth\Models\RecordManagers\User;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Reply extends \Hubleto\Erp\RecordManager
{
  public $table = 'linkedin_replies';

  /** @return BelongsTo<Message, covariant self> */
  public function MESSAGE(): BelongsTo
  {
    return $this->belongsTo(Message::class, 'id_message', 'id');
  }

  /** @return BelongsTo<User, covariant self> */
  public function OWNER(): BelongsTo
  {
    return $this->belongsTo(User::class, 'id_owner', 'id');
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
    $fReplyStatus = (int) ($filters['fReplyStatus'] ?? 0);
    if ($fReplyStatus > 0) $query = $query->where($this->table . '.status', $fReplyStatus);

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
