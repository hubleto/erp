<?php

namespace Hubleto\App\Community\Workflow\Automats\Evaluators;

use Hubleto\App\Community\Workflow\Interfaces\AutomatEvaluatorInterface;
use Hubleto\App\Community\Workflow\Models\WorkflowStep;
use Hubleto\Erp\Core;

class WorkflowCompare extends Core implements AutomatEvaluatorInterface
{

  /**
   * [Description for matches]
   *
   * @param array $condition
   * 
   * @return void
   * 
   */
  public function matches(array $condition): bool
  {
    $updatedModel = $condition['updatedModel'] ?? '';
    $updatedRecord = $condition['updatedRecord'] ?? [];
    $tagIs = $condition['tagIs'] ?? '';
    $tagIsNot = $condition['tagIsNot'] ?? '';

    /** @var WorkflowStep */
    $mWorkflowStep = $this->getModel(WorkflowStep::class);

    $step = $mWorkflowStep->record->where('id', $updatedRecord['id_workflow_step'] ?? 0);
    if (!empty($tagIs)) $step->where('tag', $tagIs);
    if (!empty($tagIsNot)) $step->whereNot('tag', $tagIsNot);
    $step = $step->first();

    return $step && $step->id > 0;
  }

}