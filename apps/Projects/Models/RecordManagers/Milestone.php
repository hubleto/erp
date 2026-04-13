<?php

namespace Hubleto\App\Community\Projects\Models\RecordManagers;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Hubleto\App\Community\Auth\Models\RecordManagers\User;

class Milestone extends \Hubleto\Erp\RecordManager
{
  public $table = 'projects_milestones';

  /** @return BelongsTo<Deal, covariant ProjectActivity> */
  public function PROJECT(): BelongsTo
  {
    return $this->belongsTo(Project::class, 'id_project', 'id');
  }

  /** @return HasMany<ProjectTask, covariant Deal> */
  public function REPORTS(): HasMany
  {
    return $this->hasMany(MilestoneReport::class, 'id_milestone', 'id');
  }

  public function RESPONSIBLE(): BelongsTo
  {
    return $this->belongsTo(User::class, 'id_responsible', 'id');
  }

  public function prepareReadQuery(mixed $query = null, int $level = 0, array|null $includeRelations = null): mixed
  {
    $query = parent::prepareReadQuery($query, $level, $includeRelations);

    $hubleto = \Hubleto\Erp\Loader::getGlobalApp();
    $idProject = $hubleto->router()->urlParamAsInteger("idProject");

    if ($idProject > 0) $query = $query->where($this->table . '.id_project', $idProject);

    if (isset($filters["fMilestoneClosed"])) {
      if ($filters["fMilestoneClosed"] == 0) $query = $query->where($this->table . '.is_closed', false);
      if ($filters["fMilestoneClosed"] == 1) $query = $query->where($this->table . '.is_closed', true);
    }

    if (isset($filters['fResponsible']) && is_array($filters['fResponsible']) && count($filters['fResponsible']) > 0) {
      $query = $query->whereIn($this->table . '.id_responsible', $filters['fResponsible']);
    }

    return $query;
  }

}
