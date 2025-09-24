<?php

namespace Hubleto\App\Community\Api\Models\RecordManagers;

use Illuminate\Database\Eloquent\Relations\HasMany;

class Key extends \Hubleto\Erp\RecordManager
{
  public $table = 'api_keys';

  /** @return HasMany<RolePermiPermissionssion, covariant UserRole> */
  public function PERMISSIONS(): HasMany {
    return $this->hasMany(Permission::class, 'id_key', 'id' );
  }

}
