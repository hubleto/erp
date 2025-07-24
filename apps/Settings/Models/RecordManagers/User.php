<?php

namespace HubletoApp\Community\Settings\Models\RecordManagers;

use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class User extends \Hubleto\Framework\RecordManager
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

  /** @return BelongsToMany<UserRole, covariant User> */
  public function TEAMS(): BelongsToMany
  {
    return $this->belongsToMany(
      Team::class,
      'teams_members',
      'id_member',
      'id_team'
    );
  }

  public function prepareReadQuery(mixed $query = null, int $level = 0): mixed
  {
    $query = parent::prepareReadQuery($query, $level);
    $query = $query->with('ROLES')->with('TEAMS')->with('DEFAULT_COMPANY');
    return $query;
  }

  public function prepareLookupQuery(string $search): mixed
  {
    $main = \HubletoMain\Loader::getGlobalApp();
    $idUser = $main->auth->getUserId();

    $query = $this;

    if (!empty($search)) {
      $query->where(function ($q) use ($search) {
        foreach ($this->model->columnNames() as $columnName) {
          $q->orWhere($this->model->table . '.' . $columnName, 'LIKE', '%' . $search . '%');
        }
      });
    }

    $query = $query->selectRaw('
      ' . $this->table . '.id,
      concat(
        ifnull(' . $this->table . '.nick, ' . $this->table . '.email),
        if(' . $this->table . '.id = ' . $idUser .  ', " (you)", "")
      ) as _LOOKUP,
      concat("rounded inline-block px-1 ", if(' . $this->table . '.id = ' . $idUser .  ', "bg-blue-100 text-blue-800", "bg-slate-100 text-slate-800")) as _LOOKUP_CLASS,
      "" as _LOOKUP_COLOR
    ');

    return $query;
  }
}
