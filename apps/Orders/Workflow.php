<?php

namespace Hubleto\App\Community\Orders;

class Workflow extends \Hubleto\App\Community\Workflow\Workflow
{

  public function loadItems(int $idWorkflow, array $filters): array
  {
    $fUser = (int) ($filters['fUser'] ?? 0);

    $mOrder = $this->getModel(Models\Order::class);
    $items = $mOrder->record->prepareReadQuery()
      ->where($mOrder->table . ".id_workflow", $idWorkflow)
      ->where($mOrder->table . ".is_closed", false)
    ;

    if ($fUser > 0) {
      $items = $items->where($mOrder->table . '.id_manager', $fUser);
    }

    $items = $items->get()?->toArray();

    foreach ($items as $key => $item) {
      $items[$key]['_DETAIL_URL'] = 'orders/' . $item['id'];
      $items[$key]['_DETAIL_VIEW'] = '@Hubleto:App:Community:Orders/WorkflowItemDetail.twig';
    }

    return $items;
  }
  
}
