<?php

namespace Hubleto\App\Community\EmailMarketing;

class Workflow extends \Hubleto\App\Community\Workflow\Workflow
{

  public function loadItems(int $idWorkflow, array $filters): array
  {
    $fOwner = (int) ($filters['fOwner'] ?? 0);

    $mEmail = $this->getModel(Models\Email::class);
    $items = $mEmail->record->prepareReadQuery()
      ->where($mEmail->table . ".id_workflow", $idWorkflow)
      ->where($mEmail->table . ".is_closed", false)
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
