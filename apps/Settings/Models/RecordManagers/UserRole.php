<?php

namespace Hubleto\App\Community\Settings\Models\RecordManagers;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class UserRole extends \Hubleto\Erp\RecordManager
{
  public $table = 'user_roles';

  // /** @return HasMany<RolePermission, covariant UserRole> */
  // public function PERMISSIONS(): HasMany {
  //   return $this->hasMany(RolePermission::class, 'id_role', 'id' );
  // }

}
