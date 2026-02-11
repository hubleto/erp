<?php

namespace Hubleto\App\Community\Projects\Models\RecordManagers;

use Hubleto\App\Community\Auth\Models\RecordManagers\User;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MilestoneReport extends \Hubleto\Erp\RecordManager
{
  public $table = 'projects_milestone_reports';

  /** @return BelongsTo<Deal, covariant ProjectActivity> */
  public function MILESTONE(): BelongsTo
  {
    return $this->belongsTo(Milestone::class, 'id_milestone', 'id');
  }

  /** @return BelongsTo<Deal, covariant ProjectActivity> */
  public function REPORTED_BY(): BelongsTo
  {
    return $this->belongsTo(User::class, 'id_reported_by', 'id');
  }

  public function prepareReadQuery(mixed $query = null, int $level = 0, array|null $includeRelations = null): mixed
  {
    $query = parent::prepareReadQuery($query, $level, $includeRelations);

    $hubleto = \Hubleto\Erp\Loader::getGlobalApp();
    $idMilestone = $hubleto->router()->urlParamAsInteger("idMilestone");

    if ($idMilestone > 0) $query = $query->where($this->table . '.id_milestone', $idMilestone);

    return $query;
  }

}
