<?php

namespace Hubleto\App\Community\Workflow\Controllers\Boards;

use Hubleto\App\Community\Workflow\Models\WorkflowHistory;
use Hubleto\Framework\Db\Column\Boolean;
use Hubleto\Framework\Helper;

class ItemsWithNotUpdatedStep extends \Hubleto\Erp\Controller
{
  public bool $hideDefaultDesktop = true;

  public function prepareView(): void
  {
    parent::prepareView();

    try {

      $model = $this->router()->urlParamAsString('model');

      $modelObj = $this->getModel($model);
      if (!$modelObj) throw new \Exception('Model does not exist.');

      $modelColumns = $modelObj->describeColumns();

      $records = [];

      $hasIsClosedBooleanColumn = (
        isset($modelColumns['is_closed'])
        && $modelColumns['is_closed'] instanceof Boolean
      );

      if (!$hasIsClosedBooleanColumn) throw new \Exception('This report can be shown only for closeable records. (must contain "Is closed" switch)');

      $mHistory = $this->getModel(WorkflowHistory::class);
      $lastChangeInfo = Helper::keyBy('id', $mHistory->record
        ->where('model', $model)
        ->selectRaw('
          workflow_history.record_id as id,
          workflow_history.id_workflow_step,
          workflow_history.id_user,
          min(TIMESTAMPDIFF(DAY, workflow_history.datetime_change, now())) as last_change_before_days,
          ' . $modelObj->table . '.is_closed as is_closed
        ')
        ->leftJoin($modelObj->table, $modelObj->table . '.id', '=', $mHistory->table . '.record_id')
        ->with('USER')
        ->with('WORKFLOW_STEP')
        ->having('is_closed', 0)
        ->groupBy('record_id')
        ->orderBy('last_change_before_days', 'desc')
        ->get()
        ->toArray()
      );

          //     TIMESTAMPDIFF(SECOND,
          //   lag(datetime_change) over (order by datetime_change),
          //   datetime_change
          // ) as diff_minute
      $records = $modelObj->record->prepareReadQuery()
        ->whereIn($modelObj->table . '.id', array_keys($lastChangeInfo))
        ->get()
        ->toArray()
      ;
      $records = $modelObj->record->prepareLookupData($records);

      foreach ($records as $key => $record) {
        $records[$key]['last_change_before_days'] = $lastChangeInfo[$record['id']]['last_change_before_days'];
        $records[$key]['USER'] = $lastChangeInfo[$record['id']]['USER'];
        $records[$key]['WORKFLOW_STEP'] = $lastChangeInfo[$record['id']]['WORKFLOW_STEP'];
      }

      usort($records, function ($a, $b) { return (int) $a['last_change_before_days'] < (int) $b['last_change_before_days']; });

      $this->viewParams['hasIsClosedBooleanColumn'] = $hasIsClosedBooleanColumn;
      $this->viewParams['model'] = $model;
      $this->viewParams['records'] = $records;
    } catch (\Throwable $e) {
      $this->viewParams['error'] = $e->getMessage();
    }

    $this->setView('@Hubleto:App:Community:Workflow/Boards/ItemsWithNotUpdatedStep.twig');
  }

}
