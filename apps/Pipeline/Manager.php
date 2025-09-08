<?php

namespace Hubleto\App\Community\Pipeline;

class Manager extends \Hubleto\Framework\Core
{

  /** @var array<string, Pipeline> */
  protected array $pipelineLoaders = [];

  public function addPipeline(\Hubleto\Framework\Interfaces\AppInterface $app, string $group, string $pipelineClass): void
  {
    $pipeline = $this->getService($pipelineClass);
    if ($pipeline instanceof Pipeline) {
      $pipeline->app = $app;
      if (!isset($this->pipelineLoaders[$group])) $this->pipelineLoaders[$group] = [];
      $this->pipelineLoaders[$group] = $pipeline;
    }
  }

  /** @return Pipeline */
  public function getPipelineLoaderForGroup(string $group): null|Pipeline
  {
    return $this->pipelineLoaders[$group] ?? null;
  }

  public function getPipeline(string $pipelineClass): Pipeline
  {
    return $this->pipelineLoaders[$pipelineClass];
  }

}
