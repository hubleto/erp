<?php

namespace Hubleto\App\Community\Worksheets\Controllers\Boards;

use Hubleto\App\Community\Auth\Models\User;
use Hubleto\App\Community\Worksheets\Models\Activity;

class Quota extends \Hubleto\Erp\Controller
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

    $workedHours = 0.00;

    $usersWorktimes = $mTasks->record->prepareReadQuery()
      ->select("worked_hours")
      ->where("date_worked", "=", date("Y-m-d"))
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
      $usersWorktimes->where("id_worker",$authProvider->getUserId());
    }

    $usersWorktimes = $usersWorktimes->get()->toArray();

    foreach ($usersWorktimes as $worktime) {
      $workedHours += (float) $worktime["worked_hours"];
    }

    $this->viewParams["quota"] = $quota;
    $this->viewParams["workedHours"] = $workedHours;

    $this->setView('@Hubleto:App:Community:Worksheets/Boards/Quota.twig');
  }

}
