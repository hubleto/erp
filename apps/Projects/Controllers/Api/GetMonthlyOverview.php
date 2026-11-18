<?php

namespace Hubleto\App\Community\Projects\Controllers\Api;

use Hubleto\App\Community\Auth\Models\User;
use Hubleto\App\Community\Projects\Models\Project;
use Hubleto\App\Community\Projects\Models\ProjectTask;
use Hubleto\App\Community\Tasks\Models\Task;
use Hubleto\App\Community\Worksheets\Models\Activity;
use Hubleto\App\Community\Worksheets\Models\ActivityType;

/**
 * Returns hours worked on a project in a given month (worksheet activities of
 * tasks assigned to the project), in total and grouped by worker, by activity
 * type and by task.
 *
 * Parameters: idProject, month (1-12), year (4 digits).
 */
class GetMonthlyOverview extends \Hubleto\Erp\Controllers\ApiController
{
  public function renderJson(): array
  {
    $idProject = $this->router()->urlParamAsInteger("idProject");
    $month = $this->router()->urlParamAsInteger("month");
    $year = $this->router()->urlParamAsInteger("year");

    if ($idProject <= 0) {
      return [
        "status" => "failed",
        "error" => $this->translate("The project is not set.")
      ];
    }

    if ($month < 1 || $month > 12 || $year < 1900 || $year > 2999) {
      return [
        "status" => "failed",
        "error" => $this->translate("The month or year is not valid.")
      ];
    }

    $mProject = $this->getService(Project::class);
    $mProjectTask = $this->getService(ProjectTask::class);
    $mTask = $this->getService(Task::class);
    $mActivity = $this->getService(Activity::class);
    $mActivityType = $this->getService(ActivityType::class);
    $mUser = $this->getService(User::class);

    try {
      // makes sure that the project exists and the user is allowed to read it
      $project = $mProject->record->prepareReadQuery()->where($mProject->table . ".id", $idProject)->first();
      if (!$project) {
        return [
          "status" => "failed",
          "error" => $this->translate("The project was not found.")
        ];
      }

      // [$dateFrom, $dateTo) is the selected month
      $monthStart = new \DateTimeImmutable(sprintf("%04d-%02d-01", $year, $month));
      $dateFrom = $monthStart->format("Y-m-d");
      $dateTo = $monthStart->modify("first day of next month")->format("Y-m-d");

      $from = '`' . $mActivity->table . '` `a`';
      $where = '
        `a`.`date_worked` >= ?
        and `a`.`date_worked` < ?
        and `a`.`id_task` in (
          select `pt`.`id_task`
          from `' . $mProjectTask->table . '` `pt`
          where `pt`.`id_project` = ?
        )
      ';
      $params = [$dateFrom, $dateTo, $idProject];

      // totalHours
      $total = $this->db()->fetchAll('
        select ifnull(sum(`a`.`worked_hours`), 0) as `worked_hours`
        from ' . $from . '
        where ' . $where . '
      ', $params);

      // workedByWorker
      $workedByWorker = $this->db()->fetchAll('
        select
          `a`.`id_worker` as `id`,
          coalesce(
            nullif(trim(concat(ifnull(`u`.`first_name`, ""), " ", ifnull(`u`.`last_name`, ""))), ""),
            `u`.`nick`,
            ""
          ) as `name`,
          sum(`a`.`worked_hours`) as `worked_hours`
        from ' . $from . '
        left join `' . $mUser->table . '` `u` on `u`.`id` = `a`.`id_worker`
        where ' . $where . '
        group by `a`.`id_worker`, `u`.`first_name`, `u`.`last_name`, `u`.`nick`
        order by `worked_hours` desc, `name` asc
      ', $params);

      // workedByActivityType
      $workedByActivityType = $this->db()->fetchAll('
        select
          `a`.`id_type` as `id`,
          ifnull(`at`.`name`, "") as `name`,
          sum(`a`.`worked_hours`) as `worked_hours`
        from ' . $from . '
        left join `' . $mActivityType->table . '` `at` on `at`.`id` = `a`.`id_type`
        where ' . $where . '
        group by `a`.`id_type`, `at`.`name`
        order by `worked_hours` desc, `name` asc
      ', $params);

      // workedByTask
      $workedByTask = $this->db()->fetchAll('
        select
          `a`.`id_task` as `id`,
          trim(concat_ws(" ", `t`.`identifier`, `t`.`title`)) as `name`,
          sum(`a`.`worked_hours`) as `worked_hours`
        from ' . $from . '
        left join `' . $mTask->table . '` `t` on `t`.`id` = `a`.`id_task`
        where ' . $where . '
        group by `a`.`id_task`, `t`.`identifier`, `t`.`title`
        order by `worked_hours` desc, `name` asc
      ', $params);
    } catch (\Exception $e) {
      return [
        "status" => "failed",
        "error" => $e->getMessage(),
      ];
    }

    return [
      "status" => "success",
      "idProject" => $idProject,
      "month" => $month,
      "year" => $year,
      "totalHours" => (float) ($total[0]['worked_hours'] ?? 0),
      "workedByWorker" => $this->normalizeRows($workedByWorker),
      "workedByActivityType" => $this->normalizeRows($workedByActivityType),
      "workedByTask" => $this->normalizeRows($workedByTask),
    ];
  }

  /**
   * Casts values fetched from database to types suitable for JSON.
   *
   * @param array $rows
   *
   * @return array
   *
   */
  private function normalizeRows(array $rows): array
  {
    return array_map(fn(array $row): array => [
      'id' => $row['id'] === null ? null : (int) $row['id'],
      'name' => (string) $row['name'],
      'worked_hours' => (float) $row['worked_hours'],
    ], $rows);
  }

}