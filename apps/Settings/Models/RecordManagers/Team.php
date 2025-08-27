<?php

namespace HubletoApp\Community\Settings\Models\RecordManagers;

use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Team extends \HubletoMain\RecordManager
{
  public $table = 'teams';

  /** @return BelongsTo<User, covariant User> */
  public function MANAGER(): BelongsTo
  {
    return $this->belongsTo(User::class, 'id_manager', 'id');
  }

  /** @return HasMany<TeamMember, covariant TeamMember> */
  public function MEMBERS(): HasMany
  {
    return $this->hasMany(TeamMember::class, 'id_team', 'id');
  }

  public function prepareReadQuery(mixed $query = null, int $level = 0): mixed
  {
    $query = parent::prepareReadQuery($query, $level);
    $query = $query->with('MEMBERS.MEMBER');
    return $query;
  }

}
