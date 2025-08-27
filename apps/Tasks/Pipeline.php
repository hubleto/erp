<?php

namespace Hubleto\App\Community\Tasks;

class Pipeline extends \Hubleto\App\Community\Pipeline\Pipeline
{

  public function loadItems(int $idPipeline, array $filters): array
  {
    $fOwner = (int) ($filters['fOwner'] ?? 0);

    $mTask = $this->getModel(Models\Task::class);
    $items = $mTask->record->prepareReadQuery()
      ->where($mTask->table . ".id_pipeline", $idPipeline)
      ->where($mTask->table . ".is_closed", false)
    ;

    if ($fOwner > 0) {
      $items = $items->where('id_owner', $fOwner);
    }

    $items = $items->get()?->toArray();

    foreach ($items as $key => $item) {
      $items[$key]['_DETAIL_URL'] = 'tasks/' . $item['id'];
    }

    return $items;
  }
  
}
