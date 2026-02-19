<?php

namespace Hubleto\App\Community\Projects;

class Workflow extends \Hubleto\App\Community\Workflow\Workflow
{

  public function loadItems(int $idWorkflow, array $filters): array
  {
    $fUser = (int) ($filters['fUser'] ?? 0);

    $mProject = $this->getModel(Models\Project::class);
    $items = $mProject->record->prepareReadQuery()
      ->with(['MILESTONES.REPORTS' => function($query) {
          $query->orderBy('projects_milestone_reports.date_report', 'desc');
      }])
      ->with('TASKS.TASK', function($q) {
        $q->where('tasks.is_closed', false);
      })
      ->where($mProject->table . ".id_workflow", $idWorkflow)
      ->where($mProject->table . ".is_closed", false)
    ;

    if ($fUser > 0) {
      $items = $items->where($mProject->table . '.id_project_manager', $fUser);
    }

    $items = $items->get()?->toArray();

    foreach ($items as $key => $item) {
      $items[$key]['_DETAIL_URL'] = 'projects/' . $item['id'];
      $items[$key]['_DETAIL_VIEW'] = '@Hubleto:App:Community:Projects/WorkflowItemDetail.twig';
    }

    return $items;
  }
  
}
