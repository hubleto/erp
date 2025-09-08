<?php

namespace Hubleto\App\Community\Pipeline\Controllers\Api;

use Exception;
use Hubleto\App\Community\Pipeline\Models\PipelineStep;

class GetPipelineStepByTag extends \Hubleto\Erp\Controllers\ApiController
{
  public function renderJson(): ?array
  {

    $idPipeline = $this->router()->urlParamAsInteger('idPipeline');
    $tag = $this->router()->urlParamAsString('tag');

    $mPipelineStep = $this->getService(PipelineStep::class);

    return $mPipelineStep->record->where('id_pipeline', $idPipeline)->where('tag', $tag)->first()?->toArray();
  }

}
