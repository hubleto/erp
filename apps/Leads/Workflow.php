<?php

namespace Hubleto\App\Community\Leads;

class Workflow extends \Hubleto\App\Community\Workflow\Workflow
{

  public function loadItems(int $idWorkflow, array $filters): array
  {
    $fOwner = (int) ($filters['fOwner'] ?? 0);

    $mLead = $this->getModel(Models\Lead::class);
    $items = $mLead->record->prepareReadQuery()
      ->where($mLead->table . ".id_workflow", $idWorkflow)
      ->where($mLead->table . ".is_closed", false)
    ;

    if ($fOwner > 0) {
      $items = $items->where('id_owner', $fOwner);
    }

    $items = $items->get()?->toArray();

    foreach ($items as $key => $item) {
      $items[$key]['_DETAIL_URL'] = 'leads/' . $item['id'];
      $items[$key]['_DETAIL_VIEW'] = '@Hubleto:App:Community:Leads/WorkflowItemDetail.twig';
    }

    return $items;
  }
  
}
