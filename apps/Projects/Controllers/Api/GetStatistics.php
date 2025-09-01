<?php

namespace Hubleto\App\Community\Projects\Controllers\Api;

use Hubleto\App\Community\Projects\Models\Project;
use Hubleto\App\Community\Projects\Models\ProjectTask;
use Hubleto\App\Community\Tasks\Models\Task;
use Hubleto\App\Community\Worksheets\Models\Activity;

class GetStatistics extends \Hubleto\Erp\Controllers\ApiController
{
  public function renderJson(): ?array
  {
    $idProject = $this->getRouter()->urlParamAsInteger("idProject");

    $statistics = [];

    $mProject = $this->getService(Project::class);
    $mTask = $this->getService(Task::class);
    $mProjectTask = $this->getService(ProjectTask::class);
    $mActivity = $this->getService(Activity::class);

    try {
      $project = $mProject->record->prepareReadQuery()->where($mProject->table.".id", $idProject)->first();
      $statistics['project'] = $project;

      $projectTasksIds = $mProjectTask->record->prepareReadQuery()
        ->where($mProjectTask->table . '.id_project', $idProject)
        ->pluck('id_task')
        ?->toArray()
      ;

      if (count($projectTasksIds) == 0) $projectTasksIds = [0];

      // $workedByMonth = $mActivity->record->prepareReadQuery()
      //   ->select($mActivity->table . '.date_worked')
      //   ->selectRaw('sum(' . $mActivity->table . '.worked_hours) as worked_hours')
      //   ->leftJoin($mTask->table, $mActivity->table . '.id_task', '=', $mTask->table . '.id')
      //   // ->whereIn($mTask->table . '.id', $projectTasksIds)
      //   ->groupByRaw('concat(year(date_worked), month(date_worked)')
      //   ->get()
      // ;
      $workedByMonth = $this->getDb()->fetchAll('
        select
          month(`' . $mActivity->table . '`.`date_worked`) as `month`,
          year(`' . $mActivity->table . '`.`date_worked`) as `year`,
          sum(`' . $mActivity->table . '`.`worked_hours`) as `worked_hours`
        from `' . $mActivity->table . '`
        left join `' . $mTask->table . '` on `' . $mTask->table . '`.`id` = `' . $mActivity->table . '`.`id_task`
        where
          `' . $mTask->table . '`.`id` in (' . join(',', $projectTasksIds) . ')
        group by
          concat(year(date_worked), month(date_worked))
      ');

      $statistics['workedByMonth'] = $workedByMonth;

    } catch (\Exception $e) {
      return [
        "status" => "failed",
        "error" => $e->getMessage(),
      ];
    }

    return $statistics;
  }

}
