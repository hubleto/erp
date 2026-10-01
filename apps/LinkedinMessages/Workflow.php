<?php

namespace Hubleto\App\Custom\LinkedinMessages;

class Workflow extends \Hubleto\App\Community\Workflow\Workflow
{

  public function loadItems(int $idWorkflow, array $filters): array
  {
    $fOwner = (int) ($filters['fOwner'] ?? 0);

    /** @var Models\Campaign */
    $mCampaign = $this->getModel(Models\Campaign::class);
    $items = $mCampaign->record->prepareReadQuery()
      ->where($mCampaign->table . '.id_workflow', $idWorkflow)
      ->where($mCampaign->table . '.is_closed', false)
    ;

    if ($fOwner > 0) {
      $items = $items->where($mCampaign->table . '.id_owner', $fOwner);
    }

    $items = $items->get()?->toArray();

    foreach ($items as $key => $item) {
      $items[$key]['_DETAIL_URL'] = 'linkedin-messages/campaigns/' . $item['id'];
      $items[$key]['_DETAIL_VIEW'] = '@Hubleto:App:Custom:LinkedinMessages/WorkflowItemDetail.twig';
    }

    return $items;
  }

}
