<?php

namespace Hubleto\App\Community\Projects\Models\RecordManagers;

use Hubleto\App\Community\Tasks\Models\RecordManagers\Task;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MilestoneTask extends \Hubleto\Erp\RecordManager
{
  public $table = 'projects_milestones_tasks';

  /** @return BelongsTo<Tag, covariant LeadTag> */
  public function MILESTONE(): BelongsTo
  {
    return $this->belongsTo(Milestone::class, 'id_milestone', 'id');
  }

  /** @return BelongsTo<Task, covariant LeadTag> */
  public function TASK(): BelongsTo
  {
    return $this->belongsTo(Task::class, 'id_task', 'id');
  }

  /**
   * [Description for prepareReadQuery]
   *
   * @param mixed|null $query
   * @param int $level
   * @param array|null|null $includeRelations
   * 
   * @return mixed
   * 
   */
  public function prepareReadQuery(mixed $query = null, int $level = 0, array|null $includeRelations = null): mixed
  {
    $query = parent::prepareReadQuery($query, $level, $includeRelations);

    $hubleto = \Hubleto\Erp\Loader::getGlobalApp();
    $idMilestone = $hubleto->router()->urlParamAsInteger("idMilestone");

    if ($idMilestone > 0) $query = $query->where($this->table . '.id_milestone', $idMilestone);

    return $query;
  }
}
