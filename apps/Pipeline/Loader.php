<?php

namespace HubletoApp\Community\Pipeline;

use HubletoApp\Community\Deals\Models\Deal;

class Loader extends \Hubleto\Framework\App
{
  public function init(): void
  {
    parent::init();

    $this->main->router->httpGet([
      '/^pipeline\/?$/' => Controllers\Home::class,
      '/^pipeline\/api\/get-pipelines\/?$/' => Controllers\Api\GetPipelines::class,
      '/^settings\/pipelines\/?$/' => Controllers\Pipelines::class,
    ]);

    $this->main->apps->community('Settings')->addSetting($this, [
      'title' => $this->translate('Pipelines'),
      'icon' => 'fas fa-bars-progress',
      'url' => 'settings/pipelines'
    ]);
  }

  public function installTables(int $round): void
  {
    if ($round == 1) {
      $mPipeline = $this->main->di->create(Models\Pipeline::class);
      $mPipelineStep = $this->main->di->create(Models\PipelineStep::class);

      $mPipeline->dropTableIfExists()->install();
      $mPipelineStep->dropTableIfExists()->install();

      $idPipeline = $mPipeline->record->recordCreate([ "name" => "Deal stage", "type" => $mPipeline::TYPE_DEAL_MANAGEMENT ])['id'];
      $mPipelineStep->record->recordCreate([ 'name' => 'Prospecting', 'order' => 1, 'color' => '#838383', 'id_pipeline' => $idPipeline , "set_result" => Deal::RESULT_UNKNOWN, "probability" => 1]);
      $mPipelineStep->record->recordCreate([ 'name' => 'Qualified to buy', 'order' => 2, 'color' => '#d8a082', 'id_pipeline' => $idPipeline, "set_result" => Deal::RESULT_UNKNOWN, "probability" => 10]);
      $mPipelineStep->record->recordCreate([ 'name' => 'Proposal & Quote Sent', 'order' => 3, 'color' => '#d1cf79', 'id_pipeline' => $idPipeline, "set_result" => Deal::RESULT_UNKNOWN , "probability" => 30]);
      $mPipelineStep->record->recordCreate([ 'name' => 'Negotiation & Adjustments', 'order' => 4, 'color' => '#79d1a5', 'id_pipeline' => $idPipeline, "set_result" => Deal::RESULT_UNKNOWN , "probability" => 50]);
      $mPipelineStep->record->recordCreate([ 'name' => 'Decision-Making Phase', 'order' => 5, 'color' => '#82b3d8', 'id_pipeline' => $idPipeline, "set_result" => Deal::RESULT_UNKNOWN , "probability" => 70]);
      $mPipelineStep->record->recordCreate([ 'name' => 'Contract Sent', 'order' => 6, 'color' => '#82d88b', 'id_pipeline' => $idPipeline, "set_result" => Deal::RESULT_UNKNOWN , "probability" => 85]);
      $mPipelineStep->record->recordCreate([ 'name' => 'Closed WON', 'order' => 7, 'color' => '#008000', 'id_pipeline' => $idPipeline, "set_result" => Deal::RESULT_WON , "probability" => 100]);
      $mPipelineStep->record->recordCreate([ 'name' => 'Closed LOST', 'order' => 8, 'color' => '#f50c0c', 'id_pipeline' => $idPipeline, "set_result" => Deal::RESULT_LOST , "probability" => 0]);

      $idPipeline = $mPipeline->record->recordCreate([ "name" => "Project phase", "type" => $mPipeline::TYPE_PROJECT_MANAGEMENT ])['id'];
      $mPipelineStep->record->recordCreate(['id_pipeline' => $idPipeline, 'name' => 'Early preparation', 'order' => 1, 'color' => '#344556']);
      $mPipelineStep->record->recordCreate(['id_pipeline' => $idPipeline, 'name' => 'Advanced preparation', 'order' => 2, 'color' => '#6830a5']);
      $mPipelineStep->record->recordCreate(['id_pipeline' => $idPipeline, 'name' => 'Final preparation', 'order' => 3, 'color' => '#3068a5']);
      $mPipelineStep->record->recordCreate(['id_pipeline' => $idPipeline, 'name' => 'Early implementation', 'order' => 4, 'color' => '#ae459f']);
      $mPipelineStep->record->recordCreate(['id_pipeline' => $idPipeline, 'name' => 'Advanced implementation', 'order' => 5, 'color' => '#a38f9a']);
      $mPipelineStep->record->recordCreate(['id_pipeline' => $idPipeline, 'name' => 'Final implementation', 'order' => 6, 'color' => '#44879a']);
      $mPipelineStep->record->recordCreate(['id_pipeline' => $idPipeline, 'name' => 'Delivery', 'order' => 7, 'color' => '#74809a']);

      $idPipeline = $mPipeline->record->recordCreate([ "name" => "Task status", "type" => $mPipeline::TYPE_TASK_MANAGEMENT ])['id'];
      $mPipelineStep->record->recordCreate(['id_pipeline' => $idPipeline, 'name' => 'In progress', 'order' => 1, 'color' => '#344556']);
      $mPipelineStep->record->recordCreate(['id_pipeline' => $idPipeline, 'name' => 'Ready to test', 'order' => 2, 'color' => '#6830a5']);
      $mPipelineStep->record->recordCreate(['id_pipeline' => $idPipeline, 'name' => 'Tested, working', 'order' => 3, 'color' => '#3068a5']);
      $mPipelineStep->record->recordCreate(['id_pipeline' => $idPipeline, 'name' => 'Tested, not working', 'order' => 4, 'color' => '#ae459f']);
      $mPipelineStep->record->recordCreate(['id_pipeline' => $idPipeline, 'name' => 'Ready to deploy', 'order' => 5, 'color' => '#a38f9a']);
      $mPipelineStep->record->recordCreate(['id_pipeline' => $idPipeline, 'name' => 'Deployed', 'order' => 6, 'color' => '#44879a']);
      $mPipelineStep->record->recordCreate(['id_pipeline' => $idPipeline, 'name' => 'Confirmed', 'order' => 7, 'color' => '#74809a']);
    }
  }

}
