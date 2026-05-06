<?php

namespace Hubleto\App\Community\Orders\Controllers\Api;

use Hubleto\App\Community\Auth\Models\User;
use Hubleto\App\Community\Projects\Models\Project;
use Hubleto\App\Community\Projects\Models\ProjectOrder;
use Hubleto\App\Community\Projects\Models\ProjectTask;
use Hubleto\App\Community\Tasks\Models\Task;
use Hubleto\App\Community\Worksheets\Models\Activity;

class GetStatistics extends \Hubleto\Erp\Controllers\ApiController
{
  public function renderJson(): array
  {
    $idOrder = $this->router()->urlParamAsInteger("idOrder");

    $statistics = [];

    $mProject = $this->getService(Project::class);
    $mProjectOrder = $this->getService(ProjectOrder::class);
    $mTask = $this->getService(Task::class);
    $mProjectTask = $this->getService(ProjectTask::class);
    $mActivity = $this->getService(Activity::class);
    $mUser = $this->getService(User::class);

    try {
      $projectIds = $mProjectOrder->record
        ->where($mProjectOrder->table . '.id_order', $idOrder)
        ->pluck('id_project');

      $projects = $mProject->record
        ->whereIn($mProject->table . ".id", $projectIds)
        ->get();

      $statistics = ['projects' => []];

      foreach ($projects  as $project) {
        $statistics['projects'][$project->id] = [];

        $statistics['projects'][$project->id]['project'] = $project;

        $averageHourlyCosts = $project->average_hourly_costs ?? 0;

        $projectTasksIds = $mProjectTask->record->prepareReadQuery()
          ->where($mProjectTask->table . '.id_project', $project->id)
          ->pluck('id_task')
          ?->toArray()
        ;

        if (count($projectTasksIds) == 0) $projectTasksIds = [0];

        // workedByUser
        $workedByUser = $this->db()->fetchAll('
          select
            `' . $mActivity->table . '`.`id_worker` as `id_worker`,
            concat(ifnull(`u`.`first_name`, ""), " ", ifnull(`u`.`last_name`, "")) as `worker_name`,
            sum(`' . $mActivity->table . '`.`worked_hours`) as `worked_hours`
          from `' . $mActivity->table . '`
          left join `' . $mTask->table . '` `t` on `t`.`id` = `' . $mActivity->table . '`.`id_task`
          left join `' . $mUser->table . '` `u` on `u`.`id` = `' . $mActivity->table . '`.`id_worker`
          where
            `t`.`id` in (' . join(',', $projectTasksIds) . ')
          group by
            `id_worker`
        ');

        $statistics['projects'][$project->id]['workedByUser'] = $workedByUser;

        // workedByMonth
        $workedByMonth = $this->db()->fetchAll('
          select
            month(`' . $mActivity->table . '`.`date_worked`) as `month`,
            year(`' . $mActivity->table . '`.`date_worked`) as `year`,
            sum(`' . $mActivity->table . '`.`worked_hours`) as `worked_hours`,
            sum(`' . $mActivity->table . '`.`worked_hours`) * ' . $averageHourlyCosts . ' as `costs`
          from `' . $mActivity->table . '`
          left join `' . $mTask->table . '` on `' . $mTask->table . '`.`id` = `' . $mActivity->table . '`.`id_task`
          where
            `' . $mTask->table . '`.`id` in (' . join(',', $projectTasksIds) . ')
          group by
            concat(year(date_worked), month(date_worked))
        ');

        $statistics['projects'][$project->id]['workedByMonth'] = $workedByMonth;

        // chargeableByMonth
        $chargeableByMonth = $this->db()->fetchAll('
          select
            month(`' . $mActivity->table . '`.`date_worked`) as `month`,
            year(`' . $mActivity->table . '`.`date_worked`) as `year`,
            sum(`' . $mActivity->table . '`.`worked_hours`) as `worked_hours`
          from `' . $mActivity->table . '`
          left join `' . $mTask->table . '` on `' . $mTask->table . '`.`id` = `' . $mActivity->table . '`.`id_task`
          where
            `' . $mTask->table . '`.`id` in (' . join(',', $projectTasksIds) . ')
            and `' . $mTask->table . '`.`is_chargeable` = 1
            and `' . $mActivity->table . '`.`is_chargeable` = 1
          group by
            concat(year(date_worked), month(date_worked))
        ');

        $statistics['projects'][$project->id]['chargeableByMonth'] = $chargeableByMonth;

      }
    } catch (\Exception $e) {
      return [
        "status" => "failed",
        "error" => $e->getMessage(),
      ];
    }

    return $statistics;
  }

}
