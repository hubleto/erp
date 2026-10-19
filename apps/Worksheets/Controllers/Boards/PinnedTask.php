<?php

namespace Hubleto\App\Community\Worksheets\Controllers\Boards;

use Hubleto\App\Community\Tasks\Models\Task;

class PinnedTask extends \Hubleto\Erp\Controller
{
  public bool $hideDefaultDesktop = true;

  public function prepareView(): void
  {
    parent::prepareView();

    $mTasks = $this->getModel(Task::class);

    $pinnedTasks = $this->router()->urlParamAsArray("pinnedTasks") ?? [];
    $tasks = $mTasks->record
      ->select("id","title")
      ->selectRaw("(
        select
          concat(
            group_concat(ifnull(leads.title, '') separator ', '),
            group_concat(ifnull(concat(deals.identifier, ' ', deals.title), '') separator ', '),
            group_concat(ifnull(concat(projects.identifier, ' ', projects.title), '') separator ', ')
          )
        from tasks t2

        left join leads_tasks on leads_tasks.id_task = t2.id
        left join leads on leads.id = leads_tasks.id_lead

        left join deals_tasks on deals_tasks.id_task = t2.id
        left join deals on deals.id = deals_tasks.id_deal

        left join projects_tasks on projects_tasks.id_task = t2.id
        left join projects on projects.id = projects_tasks.id_project

        where
          t2.id = tasks.id
          and (
            leads_tasks.id_task = tasks.id
            or deals_tasks.id_task = tasks.id
            or projects_tasks.id_task = tasks.id
          )
      ) as relatedTo
    ")
      ->whereIn("id", $pinnedTasks)
      ->where("is_closed", false)
      ->get()
      ?->toArray()
    ;

    $this->viewParams["pinnedTasks"] = $tasks;
    $this->setView('@Hubleto:App:Community:Worksheets/Boards/PinnedTasks.twig');
  }

}
