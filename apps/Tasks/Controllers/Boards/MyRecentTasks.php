<?php

namespace Hubleto\App\Community\Tasks\Controllers\Boards;

use Hubleto\App\Community\Worksheets\Models\Activity;

class MyRecentTasks extends \Hubleto\Erp\Controller
{
  public bool $hideDefaultDesktop = true;

  public function prepareView(): void
  {
    parent::prepareView();

    $mActivity = $this->getModel(Activity::class);

    $activities = $mActivity->record
      ->where("id_worker", $this->authProvider()->getUserId())
      ->where("date_worked", ">", date("Y-m-d H:i:s", strtotime("-2 weeks")))
      ->with('TASK')
      ->groupBy('id_task')
      ->get()
    ;

    $recentTasks = [];

    foreach ($activities as $activity) {
      $recentTasks[] = $activity['TASK'];
    }

    $this->viewParams['recentTasks'] = $recentTasks;

    $this->setView('@Hubleto:App:Community:Tasks/Boards/MyRecentTasks.twig');
  }

}
