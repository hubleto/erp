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

  /** @return BelongsTo<Company, covariant User> */
  public function DEFAULT_COMPANY(): BelongsTo
  {
    return $this->belongsTo(Company::class, 'id_default_company', 'id');
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
