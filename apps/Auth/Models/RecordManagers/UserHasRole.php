<?php

namespace Hubleto\App\Community\Auth\Models\RecordManagers;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class UserHasRole extends \Hubleto\Erp\RecordManager
{
  public $table = 'user_has_roles';

  public function prepareReadQuery(mixed $query = null, int $level = 0): mixed
  {
    $query = parent::prepareReadQuery($query, $level);

    $main = \Hubleto\Erp\Loader::getGlobalApp();

    if ($main->router()->isUrlParam("idUser")) {
      $query = $query->where($this->table . '.id_user', $main->router()->urlParamAsInteger("idUser"));
    }

    return $query;
  }
}
