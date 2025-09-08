<?php

namespace Hubleto\App\Community\Pipeline\Controllers\Api;

use Exception;
use Hubleto\App\Community\Pipeline\Models\Pipeline;

class GetPipelines extends \Hubleto\Erp\Controllers\ApiController
{
  public function renderJson(): ?array
  {

    $mPipeline = $this->getService(Pipeline::class);
    $pipelines = \Hubleto\Framework\Helper::keyBy('id', $mPipeline->record->prepareReadQuery()->get()?->toArray());

    return [
      "status" => "success",
      "pipelines" => $pipelines,
    ];
  }

}
