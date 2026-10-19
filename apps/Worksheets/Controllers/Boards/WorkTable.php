<?php

namespace Hubleto\App\Community\Worksheets\Controllers\Boards;

use Hubleto\App\Community\Auth\Models\User;
use Hubleto\App\Community\Worksheets\Models\Activity;

class WorkTable extends \Hubleto\Erp\Controller
{
  public bool $hideDefaultDesktop = true;

  public function prepareView(): void
  {
    parent::prepareView();

    $authProvider = $this->authProvider();

    $mTasks = $this->getModel(Activity::class);
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

    $usersWorktimes = $mTasks->record->prepareReadQuery()
      ->select("worked_hours", "date_worked")
      ->where("date_worked", ">=", $dateStart . " 00:00:00")
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
        $usersWorktimes->where("id_worker",$employee["id"]);
        $this->viewParams["employee"] = $employee["first_name"] . " " . $employee["last_name"];
      } else {
        $this->viewParams["employee"] = "N/A";
        $usersWorktimes->where("id_worker", $authProvider->getUserId());
      }
    } else {
      $usersWorktimes->where("id_worker", $authProvider->getUserId());
    }

    $usersWorktimes = $usersWorktimes->get()->toArray();

    foreach ($usersWorktimes as $workTime) {
      $year = date("Y", strtotime($workTime["date_worked"]));
      $month = date("F", strtotime($workTime["date_worked"]));
      $date = date("Y-m-d", strtotime($workTime["date_worked"]));

      if (isset($sortedWorkDays[$year][$month][$date]["hours"])) {
        $sortedWorkDays[$year][$month][$date]["hours"] += (float) $workTime["worked_hours"];
      } else {
        $sortedWorkDays[$year][$month][$date]["hours"] = (float) $workTime["worked_hours"];
      }
    }

    $this->viewParams["worksheet"] = $sortedWorkDays;
    $this->viewParams["quota"] = $quota;
    $this->setView('@Hubleto:App:Community:Worksheets/Boards/WorkTable.twig');
  }

}
