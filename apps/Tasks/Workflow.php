<?php

namespace Hubleto\App\Community\Tasks;

class Workflow extends \Hubleto\App\Community\Workflow\Workflow
{

  public function loadItems(int $idWorkflow, array $filters): array
  {
    $fOwner = (int) ($filters['fOwner'] ?? 0);

    $mTask = $this->getModel(Models\Task::class);
    $items = $mTask->record->prepareReadQuery()
      ->where($mTask->table . ".id_workflow", $idWorkflow)
      ->where($mTask->table . ".is_closed", false)
    ;

    if ($fOwner > 0) {
      $items = $items->where('id_owner', $fOwner);
    }

    $items = $items->get()?->toArray();

    foreach ($items as $key => $item) {
      $items[$key]['_DETAIL_URL'] = 'tasks/' . $item['id'];
      $items[$key]['_DETAIL_VIEW'] = '@Hubleto:App:Community:Tasks/WorkflowItemDetail.twig';
    }

    return $items;
  }
  
}
