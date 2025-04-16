<?php

namespace HubletoApp\Community\Settings\Models\RecordManagers;

use \Illuminate\Database\Eloquent\Relations\HasMany;
use \Illuminate\Database\Eloquent\Relations\BelongsTo;
use \Illuminate\Database\Eloquent\Relations\BelongsToMany;

class User extends \HubletoMain\Core\RecordManager
{

  /**
   * @var string
   */
  public $table = 'users';

  /** @return BelongsTo<Profile, covariant User> */
  public function id_active_profile(): BelongsTo
  {
    return $this->belongsTo(Profile::class, 'id_active_profile', 'id');
  }

  /** @return BelongsTo<Profile, covariant User> */
  public function PROFILE(): BelongsTo
  {
    return $this->id_active_profile();
  }

  /** @return BelongsToMany<UserRole, covariant User> */
  public function ROLES(): BelongsToMany
  {
    return $this->belongsToMany(
      UserRole::class,
      'user_has_roles',
      'id_user',
      'id_role'
    );
  }

  public function prepareReadQuery(mixed $query = null, int $level = 0): mixed
  {
    $query = parent::prepareReadQuery($query, $level);
    $query = $query->with('ROLES');
    return $query;
  }

}
