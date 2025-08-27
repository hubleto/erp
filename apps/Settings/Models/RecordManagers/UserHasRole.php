<?php

namespace HubletoApp\Community\Settings\Models\RecordManagers;

use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserHasRole extends \Hubleto\Erp\RecordManager
{
  public $table = 'user_has_roles';

  public function prepareReadQuery(mixed $query = null, int $level = 0): mixed
  {
    $query = parent::prepareReadQuery($query, $level);

    $main = \Hubleto\Erp\Loader::getGlobalApp();

    if ($main->getRouter()->isUrlParam("idUser")) {
      $query = $query->where($this->table . '.id_user', $main->getRouter()->urlParamAsInteger("idUser"));
    }

    return $query;
  }
}
