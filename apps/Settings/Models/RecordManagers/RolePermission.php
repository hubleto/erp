<?php

namespace Hubleto\App\Community\Settings\Models\RecordManagers;

use Hubleto\App\Community\Auth\Models\RecordManagers\UserRole;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class RolePermission extends \Hubleto\Erp\RecordManager
{
  public $table = 'role_permissions';

  /** @return BelongsTo<UserRole, covariant RolePermission> */
  public function ROLE(): BelongsTo
  {
    return $this->belongsTo(UserRole::class, 'id_role', 'id');
  }

  /** @return BelongsTo<Permission, covariant RolePermission> */
  public function PERMISSION(): BelongsTo
  {
    return $this->belongsTo(Permission::class, 'id_permission', 'id');
  }

  public function prepareReadQuery(mixed $query = null, int $level = 0): mixed
  {
    $query = parent::prepareReadQuery($query, $level);

    $hubleto = \Hubleto\Erp\Loader::getGlobalApp();

    if ($hubleto->router()->isUrlParam("idRole")) {
      $query = $query->where($this->table . '.id_role', $hubleto->router()->urlParamAsInteger("idRole"));
    }

    return $query;
  }
}
