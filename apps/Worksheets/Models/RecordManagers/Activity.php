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

  /**
   * [Description for prepareSelectsForReadQuery]
   *
   * @param mixed|null $query
   * @param int $level
   * @param array|null|null $includeRelations
   * 
   * @return array
   * 
   */
  public function prepareSelectsForReadQuery(mixed $query = null, int $level = 0, array|null $includeRelations = null): array
  {
    $hubleto = \Hubleto\Erp\Loader::getGlobalApp();
    $filters = $hubleto->router()->urlParamAsArray("filters");
    $selects = parent::prepareSelectsForReadQuery($query, $level, $includeRelations);

    if (isset($filters['fGroupBy']) && is_array($filters['fGroupBy'])) {
      $selects[] = 'sum(worked_hours) as total_worked_hours';
    }

    return $selects;
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
        case 'lastMonth': $query = $query->whereMonth('date_worked', date('m', strtotime('-1 month'))); break;
        case 'beforeLastMonth': $query = $query->whereMonth('date_worked', date('m', strtotime('-2 month'))); break;
        case 'thisYear': $query = $query->whereYear('date_worked', date('Y')); break;
        case 'lastYear': $query = $query->whereYear('date_worked', date('Y') - 1); break;
      }
    }

    if (isset($filters['fWorker']) && is_array($filters['fWorker']) && count($filters['fWorker']) > 0) {
      $query = $query->whereIn($this->table . '.id_worker', $filters['fWorker']);
    }

    if (isset($filters['fGroupBy'])) {
      $fGroupBy = (array) $filters['fGroupBy'];
      if (in_array('task', $fGroupBy)) $query = $query->groupBy('id_task');
      if (in_array('type', $fGroupBy)) $query = $query->groupBy('id_type');
      if (in_array('project', $fGroupBy)) $query = $query->groupBy('virt_project');
      if (in_array('deal', $fGroupBy)) $query = $query->groupBy('virt_deal');
      if (in_array('worker', $fGroupBy)) $query = $query->groupBy('id_worker');
      if (in_array('month', $fGroupBy)) $query = $query->groupBy('virt_month');
    }

    return $query;
  }

}
