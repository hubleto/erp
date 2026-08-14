<?php

namespace Hubleto\App\Community\Campaigns;

class Workflow extends \Hubleto\App\Community\Workflow\Workflow
{

  public function loadItems(int $idWorkflow, array $filters): array
  {
    $fOwner = (int) ($filters['fOwner'] ?? 0);

    $mCampaign = $this->getModel(Models\Campaign::class);
    $items = $mCampaign->record->prepareReadQuery()
      ->where($mCampaign->table . ".id_workflow", $idWorkflow)
      ->where($mCampaign->table . ".is_closed", false)
    ;

    if ($fOwner > 0) {
      $items = $items->where('id_owner', $fOwner);
    }

    $items = $items->get()?->toArray();

    foreach ($items as $key => $item) {
      $items[$key]['_DETAIL_URL'] = 'campaigns/' . $item['id'];
      $items[$key]['_DETAIL_VIEW'] = '@Hubleto:App:Community:Campaigns/WorkflowItemDetail.twig';
    }

    return $items;
  }
  
}
