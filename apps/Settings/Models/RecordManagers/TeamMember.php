<?php

namespace Hubleto\App\Community\Settings\Models\RecordManagers;


use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TeamMember extends \Hubleto\Erp\RecordManager
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
    return $this->belongsTo(\Hubleto\Framework\Models\RecordManagers\User::class, 'id_member', 'id');
  }

  public function prepareReadQuery(mixed $query = null, int $level = 0): mixed
  {
    $query = parent::prepareReadQuery($query, $level);

    $hubleto = \Hubleto\Erp\Loader::getGlobalApp();

    if ($hubleto->router()->isUrlParam("idTeam")) {
      $query = $query->where($this->table . '.id_team', $hubleto->router()->urlParamAsInteger("idTeam"));
    }

    return $query;
  }

}
