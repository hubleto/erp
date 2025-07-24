<?php

namespace HubletoApp\Community\Settings\Models\RecordManagers;

use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserHasRole extends \Hubleto\Framework\RecordManager
{
  public $table = 'user_has_roles';

  public function prepareReadQuery(mixed $query = null, int $level = 0): mixed
  {
    $query = parent::prepareReadQuery($query, $level);

    $main = \HubletoMain\Loader::getGlobalApp();

    if ($main->isUrlParam("idUser")) {
      $query = $query->where($this->table . '.id_user', $main->urlParamAsInteger("idUser"));
    }

    return $query;
  }
}
