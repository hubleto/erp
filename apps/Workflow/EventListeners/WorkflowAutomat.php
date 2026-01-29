<?php declare(strict_types=1);

namespace Hubleto\App\Community\Workflow\EventListeners;

use Hubleto\Framework\Model;

class WorkflowAutomat extends \Hubleto\Framework\EventListener implements \Hubleto\Framework\Interfaces\EventListenerInterface
{

  public function onModelAfterUpdate(Model $model, array $originalRecord, array $savedRecord): void
  {
    if (!$model || !$savedRecord) return;

    $workflows = $this->config()->get('workflowAutomats');
    if (is_string($workflows)) $workflows = @json_decode($workflows, true);
    if (!is_array($workflows)) $workflows = [];

    foreach ($workflows as $workflow) {

      $wModel = $workflow['model'] ?? '';
      $wModelObj = $this->getService($wModel);
      $wData = $wModelObj->record->where('id', $savedRecord['id'] ?? 0)->first();
      $wConditions = $workflow['conditions'] ?? [];
      $wActions = $workflow['actions'] ?? [];
      if ($wData) {
        $match = true;
        foreach ($wConditions as $condition) {
          $column = $condition['column'] ?? '';
          $operator = $condition['operator'] ?? 'equals';
          $value1 = $condition['value'] ?? null;

          $dots = substr_count($column, '.');

          if ($dots == 0) {
            $value2 = $wData->$column;
          } else if ($dots == 1) {
            [$tmp1, $tmp2] = explode('.', $column, 2);
            $value2 = $wData->$tmp1->$tmp2 ?? null;
          } else if ($dots == 2) {
            [$tmp1, $rest] = explode('.', $column, 2);
            [$tmp2, $tmp3] = explode('.', $rest, 2);
            $value2 = $wData->$tmp1->$tmp2->$tmp3 ?? null;
          } else {
            $value2 = null;
          }

          switch ($operator) {
            case '==': if ($value1 != $value2) $match = false; break;
            case '!=': if ($value1 == $value2) $match = false; break;
            case '>': if ($value1 <= $value2) $match = false; break;
            case '>=': if ($value1 < $value2) $match = false; break;
            case '<': if ($value1 >= $value2) $match = false; break;
            case '<=': if ($value1 > $value2) $match = false; break;
          }
        }

        if ($match) {
          foreach ($wActions as $action) {
            $actionType = $action['type'] ?? '';
            switch ($actionType) {
              case 'updateColumn':
                $column = $action['column'] ?? '';
                $value = $action['value'] ?? null;
                $wModelObj->record->where('id', $savedRecord['id'] ?? 0)->update([
                  $column => $value,
                ]);
              break;
              case 'setWorkflowStep':
                $idWorkflow = $action['id_workflow'] ?? 0;
                $idWorkflowStep = $action['id_workflow_step'] ?? 0;
                $wModelObj->record->where('id', $savedRecord['id'] ?? 0)->update([
                  'id_workflow' => $idWorkflow,
                  'id_workflow_step' => $idWorkflowStep,
                ]);
              break;
              case 'logMessage':
                $message = $action['message'] ?? '';
                $this->logger()->info("WorkflowProcessor: " . $message);
              break;
            }
          }
        }
      }
    }
  }

}