<?php

namespace CeremonyCrmMod\Settings\Models\Eloquent;

use \Illuminate\Database\Eloquent\Relations\HasMany;
use \Illuminate\Database\Eloquent\Relations\BelongsTo;
use \Illuminate\Database\Eloquent\Relations\BelongsToMany;

class User extends \ADIOS\Core\Model\Eloquent
{

  /**
   * @var string
   */
  public $table = 'users';

  public function id_active_profile(): BelongsTo {
    return $this->belongsTo(Profile::class, 'id_active_profile', 'id');
  }

  public function PROFILE(): BelongsTo {
    return $this->id_active_profile();
  }

  public function ROLES(): BelongsToMany {
    return $this->belongsToMany(
      UserRole::class,
      'user_has_roles',
      'id_user',
      'id_role'
    );
  }


}
