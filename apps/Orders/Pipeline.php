<?php

namespace Hubleto\App\Community\Orders;

class Pipeline extends \Hubleto\App\Community\Pipeline\Pipeline
{

  public function loadItems(int $idPipeline, array $filters): array
  {
    $fOwner = (int) ($filters['fOwner'] ?? 0);

    $mOrder = $this->getModel(Models\Order::class);
    $items = $mOrder->record->prepareReadQuery()
      ->where($mOrder->table . ".id_pipeline", $idPipeline)
      ->where($mOrder->table . ".is_closed", false)
    ;

    if ($fOwner > 0) {
      $items = $items->where('id_owner', $fOwner);
    }

    $items = $items->get()?->toArray();

    foreach ($items as $key => $item) {
      $items[$key]['_DETAIL_URL'] = 'orders/' . $item['id'];
    }

    return $items;
  }
  
}
