<?php

namespace Hubleto\App\Community\Worksheets\Models\RecordManagers;


use Hubleto\App\Community\Projects\Models\ProjectTask;
use Hubleto\App\Community\Tasks\Models\RecordManagers\Task;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Hubleto\App\Community\Auth\Models\RecordManagers\User;

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

    $hubleto = \Hubleto\Erp\Loader::getGlobalApp();

    $filters = $hubleto->router()->urlParamAsArray("filters");
    $idTask = $hubleto->router()->urlParamAsInteger("idTask");
    $idProject = $hubleto->router()->urlParamAsInteger("idProject");

    if ($idTask > 0) {
      $query = $query->where($this->table . '.id_task', $idTask);
    }

    if ($idProject > 0) {
      $mProjectTask = $hubleto->getService(ProjectTask::class);

      $projectTasksIds = $mProjectTask->record->prepareReadQuery()
        ->where($mProjectTask->table . '.id_project', $idProject)
        ->pluck('id_task')
        ?->toArray()
      ;

      if (count($projectTasksIds) == 0) $projectTasksIds = [0];

      $query = $query->whereIn($this->table . '.id_task', $projectTasksIds);
    }

    if (isset($filters['fPeriod'])) {
      switch ($filters['fPeriod']) {
        case 'today': $query = $query->whereDate('date_worked', date('Y-m-d')); break;
        case 'yesterday': $query = $query->whereDate('date_worked', date('Y-m-d', strtotime('-1 day'))); break;
        case 'last7Days': $query = $query->whereDate('date_worked', '>=', date('Y-m-d', strtotime('-7 days'))); break;
        case 'last14Days': $query = $query->whereDate('date_worked', '>=', date('Y-m-d', strtotime('-14 days'))); break;
        case 'thisMonth': $query = $query->whereMonth('date_worked', date('m')); break;
        case 'lastMonth': $query = $query->whereMonth('date_worked', date('m') - 1); break;
        case 'beforeLastMonth': $query = $query->whereMonth('date_worked', date('m') - 2); break;
        case 'thisYear': $query = $query->whereYear('date_worked', date('Y')); break;
        case 'lastYear': $query = $query->whereYear('date_worked', date('Y') - 1); break;
      }
    }

    if (isset($filters['fWorker']) && is_array($filters['fWorker']) && count($filters['fWorker']) > 0) {
      $query = $query->whereIn($this->table . '.id_worker', $filters['fWorker']);
    }

    return $query;
  }

  public function addOrderByToQuery(mixed $query, array $orderBy): mixed
  {
    if (empty($orderBy)) $query->orderBy("id", "desc");
    return parent::addOrderByToQuery($query, $orderBy);
  }

}
