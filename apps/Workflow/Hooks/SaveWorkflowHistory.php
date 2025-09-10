<?php declare(strict_types=1);

namespace Hubleto\App\Community\Workflow\Hooks;

use Hubleto\App\Community\Workflow\Models\WorkflowHistory;

class SaveWorkflowHistory extends \Hubleto\Erp\Hook
{

  public function run(string $event, array $args): void
  {
    if ($event == 'model:on-after-update') {
      $model = $args['model'];
      $savedRecord = $args['savedRecord'];

      if ($model->hasColumn('id_workflow') && $model->hasColumn('id_workflow_step')) {
        $mWorkflowHistory = $this->getService(WorkflowHistory::class);

        $lastState = $mWorkflowHistory->record
          ->where('model', get_class($model))
          ->where('record_id', $savedRecord['id'])
          ->first()
        ;

        if (
          !$lastState
          || $lastState->id_workflow != $savedRecord['id_workflow']
          || $lastState->id_workflow_step != $savedRecord['id_workflow_step']
        ) {
          $mWorkflowHistory->record->recordCreate([
            'model' => get_class($model),
            'record_id' => $savedRecord['id'],
            'datetime_change' => date('Y-m-d H:i:s'),
            'id_user' => $this->authProvider()->getUserId(),
            'id_workflow' => $savedRecord['id_workflow'] ?? 0,
            'id_workflow_step' => $savedRecord['id_workflow_step'] ?? 0,
          ]);
        }
      }
    }
  }

}