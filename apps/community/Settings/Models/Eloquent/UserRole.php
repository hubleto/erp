<?php

namespace HubletoApp\Community\Settings\Models\Eloquent;

use \Illuminate\Database\Eloquent\Relations\HasMany;
use \Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserRole extends \HubletoMain\Core\ModelEloquent
{
  public $table = 'user_roles';

  public function PERMISSIONS(): HasMany {
    return $this->hasMany(RolePermission::class, 'id_role', 'id' );
  }

}
