<?php declare(strict_types=1);

namespace HubletoApp\Community\Pipeline\Hooks;

use HubletoApp\Community\Pipeline\Models\PipelineHistory;

class SavePipelineHistory extends \Hubleto\Erp\Hook
{

  public function run(string $event, array $args): void
  {
    if ($event == 'model:on-after-update') {
      $model = $args['model'];
      $savedRecord = $args['savedRecord'];

      if ($model->hasColumn('id_pipeline') && $model->hasColumn('id_pipeline_step')) {
        $mPipelineHistory = $this->getService(PipelineHistory::class);

        $lastState = $mPipelineHistory->record
          ->where('model', get_class($model))
          ->where('record_id', $savedRecord['id'])
          ->first()
        ;

        if (
          !$lastState
          || $lastState->id_pipeline != $savedRecord['id_pipeline']
          || $lastState->id_pipeline_step != $savedRecord['id_pipeline_step']
        ) {
          $mPipelineHistory->record->recordCreate([
            'model' => get_class($model),
            'record_id' => $savedRecord['id'],
            'datetime_change' => date('Y-m-d H:i:s'),
            'id_pipeline' => $savedRecord['id_pipeline'] ?? 0,
            'id_pipeline_step' => $savedRecord['id_pipeline_step'] ?? 0,
          ]);
        }
      }
    }
  }

}