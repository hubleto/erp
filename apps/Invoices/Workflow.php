<?php

namespace Hubleto\App\Community\Invoices;

class Workflow extends \Hubleto\App\Community\Workflow\Workflow
{

  public function loadItems(int $idWorkflow, array $filters): array
  {
    // $fOwner = (int) ($filters['fOwner'] ?? 0);

    // $mInvoice = $this->getModel(Models\Invoice::class);
    // $items = $mInvoice->record->prepareReadQuery()
    //   ->where($mInvoice->table . ".id_workflow", $idWorkflow)
    //   ->where($mInvoice->table . ".is_closed", false)
    // ;

    // if ($fOwner > 0) {
    //   $items = $items->where('id_owner', $fOwner);
    // }

    // $items = $items->get()?->toArray();

    // foreach ($items as $key => $item) {
    //   $items[$key]['_DETAIL_URL'] = 'invoices/' . $item['id'];
    //   $items[$key]['_DETAIL_VIEW'] = '@Hubleto:App:Community:Invoices/WorkflowItemDetail.twig';
    // }

    $items = [];

    return $items;
  }
  
}
