<?php

namespace Hubleto\App\Community\Projects;

class Pipeline extends \Hubleto\App\Community\Pipeline\Pipeline
{

  public function loadItems(int $idPipeline, array $filters): array
  {
    $fOwner = (int) ($filters['fOwner'] ?? 0);

    $mProject = $this->getModel(Models\Project::class);
    $items = $mProject->record->prepareReadQuery()
      ->where($mProject->table . ".id_pipeline", $idPipeline)
      ->where($mProject->table . ".is_closed", false)
    ;

    if ($fOwner > 0) {
      $items = $items->where('id_owner', $fOwner);
    }

    $items = $items->get()?->toArray();

    foreach ($items as $key => $item) {
      $items[$key]['_DETAIL_URL'] = 'projects/' . $item['id'];
      $items[$key]['_DETAIL_VIEW'] = '@Hubleto:App:Community:Projects/PipelineItemDetail.twig';
    }

    return $items;
  }
  
}
