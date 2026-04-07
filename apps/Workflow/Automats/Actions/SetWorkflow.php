<?php

namespace Hubleto\App\Community\Workflow\Automats\Actions;

use Hubleto\App\Community\Workflow\Interfaces\AutomatActionInterface;
use Hubleto\App\Community\Workflow\Models\WorkflowStep;
use Hubleto\Erp\Core;

class SetWorkflow extends Core implements AutomatActionInterface
{

  /**
   * [Description for execute]
   *
   * @param array $arguments
   * 
   * @return void
   * 
   */
  public function execute(array $arguments): void
  {
    $updatedModel = (string) $arguments['updatedModel'] ?? '';
    $updatedRecord = (array) $arguments['updatedRecord'] ?? [];
    $tag = (string) $arguments['tag'] ?? '';

    $recordId = $updatedRecord['id'] ?? 0;

    $modelObject = $this->getModel($updatedModel);
    $record = $modelObject->record->where($modelObject->table . '.id', $recordId)->first();

    if ($record) {
      $idWorkflow = $record->id_workflow;
      if ($idWorkflow > 0) {
        /** @var WorkflowStep */
        $mWorkflowStep = $this->getModel(WorkflowStep::class);

        $step = $mWorkflowStep->record
          ->where('id_workflow', $idWorkflow)
          ->where('tag', $tag)
          ->first();

        if ($step) {
          $modelObject->record->where('id', $recordId)->update([
            'id_workflow_step' => $step->id,
          ]);
        }
      }
    }

  }
}