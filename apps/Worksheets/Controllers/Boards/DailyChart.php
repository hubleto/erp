<?php

namespace Hubleto\App\Community\Worksheets\Controllers\Boards;

use Hubleto\App\Community\Auth\Models\User;
use Hubleto\App\Community\Worksheets\Models\Activity;

class DailyChart extends \Hubleto\App\Community\Dashboards\Controller
{
  public bool $hideDefaultDesktop = true;

  public function prepareView(): void
  {
    parent::prepareView();

    $authProvider = $this->authProvider();

    $mActivity = $this->getModel(Activity::class);
    $mUser = $this->getModel(User::class);

    $quota = $this->router()->urlParamAsFloat("quota") > 0 ? $this->router()->urlParamAsFloat("quota") : 8;
    $employeeEmail = $this->router()->urlParamAsString("employeeEmail") != "" ? $this->router()->urlParamAsString("employeeEmail") : null;
    $range = $this->router()->urlParamAsInteger("range") > 0 ? $this->router()->urlParamAsInteger("range") : 30;
    $today = date("Y-m-d");
    $dateStart = date("Y-m-d", strtotime("-".$range." days", strtotime($today)));

    //inicialize today
    $dayStr = date("D", strtotime($today));
    $year = date("Y", strtotime($today));
    $month = date("F", strtotime($today));
    $dateCounter = date("Y-m-d", strtotime($today));

    $sortedWorkDays[$year][$month][$dateCounter]["hours"] = 0;
    if ($dayStr == "Sat" || $dayStr == "Sun") $sortedWorkDays[$year][$month][$dateCounter]["weekend"] = true;

    while ($dateStart != $dateCounter) {
      $dayStr = date("D", strtotime("-1 day", strtotime($dateCounter)));
      $year = date("Y", strtotime("-1 day", strtotime($dateCounter)));
      $month = date("F", strtotime("-1 day", strtotime($dateCounter)));
      $dateCounter = date("Y-m-d", strtotime("-1 day", strtotime($dateCounter)));

      if ($dayStr == "Sat" || $dayStr == "Sun") $sortedWorkDays[$year][$month][$dateCounter]["weekend"] = true;
      $sortedWorkDays[$year][$month][$dateCounter]["hours"] = 0;
    }

    $activities = $mActivity->record->prepareReadQuery()
      ->where("date_worked", ">=", $dateStart . " 00:00:00")
      ->with("TASK.PROJECTS")
    ;

    if (!empty($employeeEmail) && (
      $authProvider->userHasRole(User::TYPE_ADMINISTRATOR) ||
      $authProvider->userHasRole(User::TYPE_CHIEF_OFFICER) ||
      $authProvider->userHasRole(User::TYPE_MANAGER)
    )) {
      $employee = $mUser->record->prepareReadQuery()
        ->select($mUser->getFullTableSqlName().".id", "first_name", "last_name")
        ->where("email", $employeeEmail)
        ->first()
        ?->toArray()
      ;

      if ($employee) {
        $activities->where("id_worker",$employee["id"]);
        $this->viewParams["employee"] = $employee["first_name"] . " " . $employee["last_name"];
      } else {
        $this->viewParams["employee"] = "N/A";
        $activities->where("id_worker", $authProvider->getUserId());
      }
    } else {
      $activities->where("id_worker", $authProvider->getUserId());
    }

    $activities = $activities->get();

    foreach ($activities as $activity) {
      $year = date("Y", strtotime($activity->date_worked));
      $month = date("F", strtotime($activity->date_worked));
      $date = date("Y-m-d", strtotime($activity->date_worked));

      if (!isset($sortedWorkDays[$year][$month][$date]["tasks"][$activity->TASK->id])) {
        $sortedWorkDays[$year][$month][$date]["tasks"][$activity->TASK->id] = $activity->TASK->toArray();
        $sortedWorkDays[$year][$month][$date]["tasks"][$activity->TASK->id]['_WORKED_HOURS'] = 0;
        $sortedWorkDays[$year][$month][$date]["tasks"][$activity->TASK->id]['_DESCRIPTIONS'] = [];
      }
      $sortedWorkDays[$year][$month][$date]["tasks"][$activity->TASK->id]['_WORKED_HOURS'] += (float) $activity->worked_hours;
      $sortedWorkDays[$year][$month][$date]["tasks"][$activity->TASK->id]['_DESCRIPTIONS'][] = $activity->description;

      if (!isset($sortedWorkDays[$year][$month][$date]["hours"])) {
        $sortedWorkDays[$year][$month][$date]["hours"] = 0;
      }
      $sortedWorkDays[$year][$month][$date]["hours"] += (float) $activity->worked_hours;
    }

    $this->viewParams["worksheet"] = $sortedWorkDays;
    $this->viewParams["quota"] = $quota;

    $this->setView('@Hubleto:App:Community:Worksheets/Boards/DailyChart.twig');
  }

}
