<?php

namespace Hubleto\App\Community\Deals;

class Pipeline extends \Hubleto\App\Community\Pipeline\Pipeline
{

  public function loadItems(int $idPipeline, array $filters): array
  {
    $fOwner = (int) ($filters['fOwner'] ?? 0);

    $mDeal = $this->getModel(Models\Deal::class);
    $items = $mDeal->record->prepareReadQuery()
      ->where($mDeal->table . ".id_pipeline", $idPipeline)
      ->where($mDeal->table . ".is_closed", false)
    ;

    if ($fOwner > 0) {
      $items = $items->where('id_owner', $fOwner);
    }

    $items = $items->get()?->toArray();

    foreach ($items as $key => $item) {
      $items[$key]['_DETAIL_URL'] = 'deals/' . $item['id'];
      $items[$key]['_DETAIL_VIEW'] = '@Hubleto:App:Community:Deals/PipelineItemDetail.twig';
    }

    return $items;
  }
  
}
