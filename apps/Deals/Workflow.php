<?php

namespace Hubleto\App\Community\Deals;

class Workflow extends \Hubleto\App\Community\Workflow\Workflow
{

  public function loadItems(int $idWorkflow, array $filters): array
  {
    $fOwner = (int) ($filters['fOwner'] ?? 0);

    $mDeal = $this->getModel(Models\Deal::class);
    $items = $mDeal->record->prepareReadQuery()
      ->where($mDeal->table . ".id_workflow", $idWorkflow)
      ->where($mDeal->table . ".is_closed", false)
    ;

    if ($fOwner > 0) {
      $items = $items->where('id_owner', $fOwner);
    }

    $items = $items->get()?->toArray();

    foreach ($items as $key => $item) {
      $items[$key]['_DETAIL_URL'] = 'deals/' . $item['id'];
      $items[$key]['_DETAIL_VIEW'] = '@Hubleto:App:Community:Deals/WorkflowItemDetail.twig';
    }

    return $items;
  }
  
}
