<?php

namespace HubletoApp\Community\Pipeline\Controllers\Api;

use Exception;
use HubletoApp\Community\Pipeline\Models\Pipeline;

class GetPipelines extends \HubletoMain\Core\Controllers\ApiController
{

  public function renderJson(): ?array
  {

    $mPipeline = new Pipeline($this->main);
    $pipelines = \ADIOS\Core\Helper::keyBy('id', $mPipeline->record->prepareReadQuery()->get()?->toArray());

    return [
      "status" => "success",
      "pipelines" => $pipelines,
    ];
  }

}
