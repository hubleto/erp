<?php

namespace CeremonyCrmApp\Modules\Core\Settings\Models\Eloquent;

use \Illuminate\Database\Eloquent\Relations\HasMany;
use \Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class RolePermission extends \ADIOS\Core\Model\Eloquent
{
  public $table = 'role_permissions';

  public function ROLE(): BelongsTo {
    return $this->belongsTo(UserRole::class, 'id_role', 'id');
  }
  public function PERMISSION(): BelongsTo {
    return $this->belongsTo(Permission::class, 'id_permission', 'id');
  }
}
