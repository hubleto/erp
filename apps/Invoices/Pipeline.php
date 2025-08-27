<?php

namespace HubletoApp\Community\Invoices;

class Pipeline extends \HubletoApp\Community\Pipeline\Pipeline
{

  public function loadItems(int $idPipeline, array $filters): array
  {
    $fOwner = (int) ($filters['fOwner'] ?? 0);

    $mInvoice = $this->getModel(Models\Invoice::class);
    $items = $mInvoice->record->prepareReadQuery()
      ->where($mInvoice->table . ".id_pipeline", $idPipeline)
      ->where($mInvoice->table . ".is_closed", false)
    ;

    if ($fOwner > 0) {
      $items = $items->where('id_owner', $fOwner);
    }

    $items = $items->get()?->toArray();

    foreach ($items as $key => $item) {
      $items[$key]['_DETAIL_URL'] = 'invoices/' . $item['id'];
    }

    return $items;
  }
  
}
