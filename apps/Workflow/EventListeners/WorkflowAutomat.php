<?php declare(strict_types=1);

namespace Hubleto\App\Community\Workflow\EventListeners;

use Hubleto\App\Community\Workflow\AutomatManager;
use Hubleto\Framework\Interfaces\ModelInterface;

class WorkflowAutomat extends \Hubleto\Framework\EventListener implements \Hubleto\Framework\Interfaces\EventListenerInterface
{

  public function onModelAfterUpdate(ModelInterface $model, array $originalRecord, array $savedRecord): void
  {
    if (!$model || !$savedRecord) return;

    foreach (AutomatManager::getAutomats('onModelAfterUpdate') as $automat) {

      try {
        $conditions = $automat['conditions'] ?? [];

        if (
          empty($triggerConditions['updatedModel'])
          || $triggerConditions['updatedModel'] == get_class($model)
        ) {
          $match = true;
          foreach ($conditions as $condition) {
            /** @var Hubleto\App\Community\Workflow\Interfaces\AutomatEvaluatorInterface */
            $evaluator = $this->getService($condition['evaluator'] ?? '');

            $arguments = $condition['arguments'] ?? [];
            $arguments['updatedModel'] = get_class($model);
            $arguments['updatedRecord'] = $savedRecord;

            $match = $evaluator->matches($arguments);
            if (!$match) break;
          }

          if ($match) {
            $actions = $automat['actions'] ?? [];
            foreach ($actions as $action) {
              /** @var Hubleto\App\Community\Workflow\Interfaces\AutomatActionInterface */
              $actionObject = $this->getService($action['action'] ?? '');

              $arguments = $action['arguments'] ?? [];
              $arguments['updatedModel'] = get_class($model);
              $arguments['updatedRecord'] = $savedRecord;

              $actionObject->execute($arguments);
            }
          }
        }
      } catch (\Throwable $e) {
        //
      }

    }
  }

}