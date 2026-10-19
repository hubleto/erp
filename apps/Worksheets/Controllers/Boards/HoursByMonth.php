<?php

namespace Hubleto\App\Community\Worksheets\Controllers\Boards;

use Hubleto\App\Community\Auth\Models\User;
use Hubleto\App\Community\Worksheets\Models\Activity;
use Illuminate\Database\Capsule\Manager as DB;

class HoursByMonth extends \Hubleto\Erp\Controller
{
  public bool $hideDefaultDesktop = true;

  public function prepareView(): void
  {
    parent::prepareView();

    $authProvider = $this->authProvider();

    $mTasks = $this->getModel(Activity::class);
    $mUser = $this->getModel(User::class);

    $employeeEmail = $this->router()->urlParamAsString("employeeEmail") != "" ? $this->router()->urlParamAsString("employeeEmail") : null;
    $year = $this->router()->urlParamAsInteger("year") > 0 ? $this->router()->urlParamAsString("year") : date("Y");

    $sortedMonths = [
      1 => ["title" => "January", "value" => null],
      2 => ["title" => "February", "value" => null],
      3 => ["title" => "March", "value" => null],
      4 => ["title" => "April", "value" => null],
      5 => ["title" => "May", "value" => null],
      6 => ["title" => "June", "value" => null],
      7 => ["title" => "July", "value" => null],
      8 => ["title" => "August", "value" => null],
      9 => ["title" => "September", "value" => null],
      10 => ["title" => "October", "value" => null],
      11 => ["title" => "November", "value" => null],
      12 => ["title" => "December", "value" => null]
    ];

    $usersWorktimes = $mTasks->record->prepareReadQuery()
      ->select(DB::raw("MONTH(date_worked) as month"), DB::raw("SUM(worked_hours) as worked_hours"))
      ->where(DB::raw("YEAR(date_worked)"), $year)
      ->groupBy(DB::raw("MONTH(date_worked)"))
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
        $usersWorktimes->where("id_worker", $employee["id"]);
        $this->viewParams["employee"] = $employee["first_name"] . " " . $employee["last_name"];
      } else {
        $this->viewParams["employee"] = "N/A";
        $usersWorktimes->where("id_worker", $authProvider->getUserId());
      }
    } else {
      $usersWorktimes->where("id_worker", $authProvider->getUserId());
    }

    $usersWorktimes = $usersWorktimes->get()->toArray();

    foreach ($usersWorktimes as $workSummary) {
      $sortedMonths[$workSummary["month"]]["value"] = $workSummary["worked_hours"];
    }

    $this->viewParams["sortedMonths"] = $sortedMonths;
    $this->viewParams["year"] = $year;
    $this->setView('@Hubleto:App:Community:Worksheets/Boards/HoursByMonth.twig');
  }

}
