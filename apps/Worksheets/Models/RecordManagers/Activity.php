<?php

namespace Hubleto\App\Community\Worksheets\Models\RecordManagers;


use Hubleto\App\Community\Projects\Models\ProjectTask;
use Hubleto\App\Community\Tasks\Models\RecordManagers\Task;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Activity extends \Hubleto\Erp\RecordManager
{
  public $table = 'worksheet_activities';

  public function WORKER(): BelongsTo
  {
    return $this->belongsTo(User::class, 'id_worker', 'id');
  }

  public function TASK(): BelongsTo
  {
    return $this->belongsTo(Task::class, 'id_task', 'id');
  }

  public function TYPE(): BelongsTo
  {
    return $this->belongsTo(ActivityType::class, 'id_type', 'id');
  }

  public function prepareReadQuery(mixed $query = null, int $level = 0): mixed
  {
    $query = parent::prepareReadQuery($query, $level);

    $main = \Hubleto\Erp\Loader::getGlobalApp();

    $idTask = $main->router()->urlParamAsInteger("idTask");
    $idProject = $main->router()->urlParamAsInteger("idProject");

    if ($idTask > 0) {
      $query = $query->where($this->table . '.id_task', $idTask);
    }

    if ($idProject > 0) {
      $mProjectTask = $main->getService(ProjectTask::class);

      $projectTasksIds = $mProjectTask->record->prepareReadQuery()
        ->where($mProjectTask->table . '.id_project', $idProject)
        ->pluck('id_task')
        ?->toArray()
      ;

      if (count($projectTasksIds) == 0) $projectTasksIds = [0];

      $query = $query->whereIn($this->table . '.id_task', $projectTasksIds);
    }

    // Uncomment and modify these lines if you want to apply default filters to your model.
    // $filters = $main->router()->urlParamAsArray("filters");
    // if (isset($filters["fArchive"]) && $filters["fArchive"] == 1) $query = $query->where("customers.is_active", false);
    // else $query = $query->where("customers.is_active", true);

    return $query;
  }

}
