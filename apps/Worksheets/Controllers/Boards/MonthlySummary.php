<?php

namespace Hubleto\App\Community\Worksheets\Controllers\Boards;

use Hubleto\App\Community\Auth\Models\User;
use Hubleto\App\Community\Worksheets\Models\Activity;

class MonthlySummary extends \Hubleto\App\Community\Dashboards\Controller
{
  public bool $hideDefaultDesktop = true;

  public function prepareView(): void
  {
    parent::prepareView();

    $authProvider = $this->authProvider();

    $mActivity = $this->getModel(Activity::class);
    $mUser = $this->getModel(User::class);

    $employeeEmail = $this->router()->urlParamAsString("employeeEmail", $authProvider->getUserEmail());

    $employee = $mUser->record->where('email', $employeeEmail)->first();

    $summary = null;
    
    if ($employee) {
      $summary = $mActivity->record
        ->selectRaw("
          sum(`worked_hours`) as `worked_hours_total`,
          month(`date_worked`) as `month`,
          year(`date_worked`) as `year`
        ")
        ->where("date_worked", ">=", date("Y-m-01", strtotime("-1 year")))
        ->where("id_worker", $employee["id"])
        ->groupByRaw("year, month")
        ->orderByRaw("year desc, month desc")
        ->get()
      ;
    }

    $this->viewParams["summary"] = $summary;
    $this->viewParams["employee"] = $employee;
    $this->viewParams["employeeEmail"] = $employeeEmail;

    $this->setView('@Hubleto:App:Community:Worksheets/Boards/MonthlySummary.twig');
  }

}
