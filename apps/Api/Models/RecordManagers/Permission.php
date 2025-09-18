<?php

namespace Hubleto\App\Community\Api\Models\RecordManagers;

use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Permission extends \Hubleto\Erp\RecordManager
{
  public $table = 'api_permissions';

  public function KEY(): BelongsTo
  {
    return $this->belongsTo(Key::class, 'id_key', 'id');
  }

  public function prepareReadQuery(mixed $query = null, int $level = 0): mixed
  {
    $query = parent::prepareReadQuery($query, $level);

    $hubleto = \Hubleto\Erp\Loader::getGlobalApp();

    $idKey = $hubleto->router()->urlParamAsInteger("idKey");
    if ($idKey > 0) $query = $query->where($this->table . '.id_key', $idKey);

    return $query;
  }

}
