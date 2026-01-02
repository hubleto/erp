<?php

namespace Hubleto\App\Community\Auth\Models\RecordManagers;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class UserHasRole extends \Hubleto\Erp\RecordManager
{
  public $table = 'user_has_roles';

  public function prepareReadQuery(mixed $query = null, int $level = 0, array|null $includeRelations = null): mixed
  {
    $query = parent::prepareReadQuery($query, $level, $includeRelations);

    $hubleto = \Hubleto\Erp\Loader::getGlobalApp();

    if ($hubleto->router()->isUrlParam("idUser")) {
      $query = $query->where($this->table . '.id_user', $hubleto->router()->urlParamAsInteger("idUser"));
    }

    return $query;
  }
}
