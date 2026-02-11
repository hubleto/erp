<?php

namespace Hubleto\App\Community\Projects\Models\RecordManagers;

use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Milestone extends \Hubleto\Erp\RecordManager
{
  public $table = 'projects_milestones';

  /** @return BelongsTo<Deal, covariant ProjectActivity> */
  public function PROJECT(): BelongsTo
  {
    return $this->belongsTo(Project::class, 'id_project', 'id');
  }

  public function prepareReadQuery(mixed $query = null, int $level = 0, array|null $includeRelations = null): mixed
  {
    $query = parent::prepareReadQuery($query, $level, $includeRelations);

    $hubleto = \Hubleto\Erp\Loader::getGlobalApp();
    $idProject = $hubleto->router()->urlParamAsInteger("idProject");

    if ($idProject > 0) $query = $query->where($this->table . '.id_project', $idProject);

    return $query;
  }

}
