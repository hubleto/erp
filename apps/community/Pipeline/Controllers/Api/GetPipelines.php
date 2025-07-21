<?php

namespace HubletoApp\Community\Pipeline\Controllers\Api;

use Exception;
use HubletoApp\Community\Pipeline\Models\Pipeline;

class GetPipelines extends \HubletoMain\Core\Controllers\ApiController
{
  public function renderJson(): ?array
  {

    $mPipeline = $this->main->di->create(Pipeline::class);
    $pipelines = \ADIOS\Core\Helper::keyBy('id', $mPipeline->record->prepareReadQuery()->get()?->toArray());

    return [
      "status" => "success",
      "pipelines" => $pipelines,
    ];
  }

}
