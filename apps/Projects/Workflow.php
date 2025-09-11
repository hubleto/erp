<?php

namespace Hubleto\App\Community\Projects;

class Workflow extends \Hubleto\App\Community\Workflow\Workflow
{

  public function loadItems(int $idWorkflow, array $filters): array
  {
    $fOwner = (int) ($filters['fOwner'] ?? 0);

    $mProject = $this->getModel(Models\Project::class);
    $items = $mProject->record->prepareReadQuery()
      ->with('TASKS.TASK')
      ->where($mProject->table . ".id_workflow", $idWorkflow)
      ->where($mProject->table . ".is_closed", false)
    ;

    if ($fOwner > 0) {
      $items = $items->where('id_owner', $fOwner);
    }

    $items = $items->get()?->toArray();

    foreach ($items as $key => $item) {
      $items[$key]['_DETAIL_URL'] = 'projects/' . $item['id'];
      $items[$key]['_DETAIL_VIEW'] = '@Hubleto:App:Community:Projects/WorkflowItemDetail.twig';
    }

    return $items;
  }
  
}
