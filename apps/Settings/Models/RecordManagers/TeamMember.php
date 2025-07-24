<?php

namespace HubletoApp\Community\Settings\Models\RecordManagers;

use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TeamMember extends \Hubleto\Framework\RecordManager
{
  public $table = 'teams_members';

  /** @return BelongsTo<Team, covariant User> */
  public function TEAM(): BelongsTo
  {
    return $this->belongsTo(Team::class, 'id_team', 'id');
  }

  /** @return BelongsTo<User, covariant User> */
  public function MEMBER(): BelongsTo
  {
    return $this->belongsTo(User::class, 'id_member', 'id');
  }

  public function prepareReadQuery(mixed $query = null, int $level = 0): mixed
  {
    $query = parent::prepareReadQuery($query, $level);

    $main = \HubletoMain\Loader::getGlobalApp();

    if ($main->isUrlParam("idTeam")) {
      $query = $query->where($this->table . '.id_team', $main->urlParamAsInteger("idTeam"));
    }

    return $query;
  }

}
