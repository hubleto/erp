<?php

namespace Hubleto\App\Community\PriceLists;

class Workflow extends \Hubleto\App\Community\Workflow\Workflow
{

  public function loadItems(int $idWorkflow, array $filters): array
  {
    $fOwner = (int) ($filters['fOwner'] ?? 0);

    /** @var Models\PriceList $mPriceList */
    $mPriceList = $this->getModel(Models\PriceList::class);
    $items = $mPriceList->record->prepareReadQuery()
      ->where($mPriceList->table . '.id_workflow', $idWorkflow)
      ->where($mPriceList->table . '.status', '!=', Models\PriceList::STATUS_ARCHIVED)
    ;

    if ($fOwner > 0) {
      $items = $items->where($mPriceList->table . '.id_owner', $fOwner);
    }

    $items = $items->get()?->toArray();

    foreach ($items as $key => $item) {
      $items[$key]['_DETAIL_URL'] = 'price-lists/' . $item['id'];
      $items[$key]['_DETAIL_VIEW'] = '@Hubleto:App:Community:PriceLists/WorkflowItemDetail.twig';
    }

    return $items;
  }

}
