<?php

namespace Hubleto\App\Community\Workflow;

class Manager extends \Hubleto\Framework\Core
{

  /** @var array<string, Workflow> */
  protected array $workflowLoaders = [];

  public function addWorkflow(\Hubleto\Framework\Interfaces\AppInterface $app, string $group, string $workflowClass): void
  {
    $workflow = $this->getService($workflowClass);
    if ($workflow instanceof Workflow) {
      $workflow->app = $app;
      if (!isset($this->workflowLoaders[$group])) $this->workflowLoaders[$group] = [];
      $this->workflowLoaders[$group] = $workflow;
    }
  }

  /** @return Workflow */
  public function getWorkflowLoaderForGroup(string $group): null|Workflow
  {
    return $this->workflowLoaders[$group] ?? null;
  }

  public function getWorkflow(string $workflowClass): Workflow
  {
    return $this->workflowLoaders[$workflowClass];
  }

}
