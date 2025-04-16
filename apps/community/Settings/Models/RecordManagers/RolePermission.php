<?php

namespace HubletoApp\Community\Settings\Models\RecordManagers;

use \Illuminate\Database\Eloquent\Relations\HasMany;
use \Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class RolePermission extends \HubletoMain\Core\RecordManager
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
}
