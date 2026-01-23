<?php declare(strict_types=1);

namespace Hubleto\App\Community\Workflow\EventListeners;

use Hubleto\App\Community\Workflow\Models\WorkflowHistory;
use Hubleto\Erp\Model;

class SaveWorkflowHistory extends \Hubleto\Framework\EventListener implements \Hubleto\Framework\Interfaces\EventListenerInterface
{

  public function onModelAfterUpdate(Model $model, array $originalRecord, array $savedRecord): void
  {
    if (!$model || !$savedRecord) return;

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
          'id_user' => $this->getService(\Hubleto\Framework\AuthProvider::class)->getUserId(),
          'id_workflow' => $savedRecord['id_workflow'] ?? 0,
          'id_workflow_step' => $savedRecord['id_workflow_step'] ?? 0,
        ]);
      }
    }
  }

}