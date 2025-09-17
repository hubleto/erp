<?php

namespace Hubleto\App\Community\Api\Models\RecordManagers;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Hubleto\App\Community\Settings\Models\RecordManagers\User;

class Usage extends \Hubleto\Erp\RecordManager
{
  public $table = 'api_usages';

  public function KEY(): BelongsTo
  {
    return $this->belongsTo(Key::class, 'id_key', 'id');
  }

  public function prepareReadQuery(mixed $query = null, int $level = 0): mixed
  {
    $query = parent::prepareReadQuery($query, $level);

    $main = \Hubleto\Erp\Loader::getGlobalApp();

    $idKey = $main->router()->urlParamAsInteger("idKey");
    if ($idKey > 0) $query = $query->where($this->table . '.id_key', $idKey);

    return $query;
  }

}
