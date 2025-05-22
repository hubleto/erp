<?php

namespace HubletoApp\Community\Pipeline;

use HubletoApp\Community\Deals\Models\Deal;

class Loader extends \HubletoMain\Core\App
{

  public function init(): void
  {
    parent::init();

    $this->main->router->httpGet([
      '/^pipeline\/?$/' => Controllers\Home::class,
      '/^settings\/pipelines\/?$/' => Controllers\Pipelines::class,
    ]);


    $this->main->addSetting($this, [
      'title' => $this->translate('Pipelines'),
      'icon' => 'fas fa-bars-progress',
      'url' => 'settings/pipelines'
    ]);
  }

  public function installTables(int $round): void
  {
    if ($round == 1) {
      $mPipeline = new Models\Pipeline($this->main);
      $mPipelineStep = new Models\PipelineStep($this->main);

      $mPipeline->dropTableIfExists()->install();
      $mPipelineStep->dropTableIfExists()->install();

      // $mPipeline->record->recordCreate([ "name" => "New customer" ]);
      // $mPipelineStep->record->recordCreate([ 'name' => 'New', 'order' => 1, 'color' => '#4080A0', 'id_pipeline' => $idPipeline , "set_result" => Deal::RESULT_PENDING, "probability" => 20]);
      // $mPipelineStep->record->recordCreate([ 'name' => 'In Progress', 'order' => 2, 'color' => '#A04020', 'id_pipeline' => $idPipeline, "set_result" => Deal::RESULT_PENDING, "probability" => 50]);
      // $mPipelineStep->record->recordCreate([ 'name' => 'Closed', 'order' => 3, 'color' => '#006060', 'id_pipeline' => $idPipeline, "set_result" => Deal::RESULT_WON , "probability" => 100]);
      // $mPipelineStep->record->recordCreate([ 'name' => 'Lost', 'order' => 4, 'color' => '#f50c0c', 'id_pipeline' => $idPipeline, "set_result" => Deal::RESULT_LOST , "probability" => 0]);

      // $mPipeline->record->recordCreate([ "name" => "Existing customer" ]);
      // $mPipelineStep->record->recordCreate([ 'name' => 'Start', 'order' => 1, 'color' => '#405060', 'id_pipeline' => 2, "set_result" => Deal::RESULT_PENDING, "probability" => 1]);
      // $mPipelineStep->record->recordCreate([ 'name' => 'Client Contacted', 'order' => 2, 'color' => '#800000', 'id_pipeline' => 2, "set_result" => Deal::RESULT_PENDING, "probability" => 15]);
      // $mPipelineStep->record->recordCreate([ 'name' => 'In Progress', 'order' => 3, 'color' => '#808000', 'id_pipeline' => 2, "set_result" => Deal::RESULT_PENDING, "probability" => 50 ]);
      // $mPipelineStep->record->recordCreate([ 'name' => 'Ended', 'order' => 4, 'color' => '#002080', 'id_pipeline' => 2, "set_result" => Deal::RESULT_WON, "probability" => 100 ]);
      // $mPipelineStep->record->recordCreate([ 'name' => 'Lost', 'order' => 5, 'color' => '#f50c0c', 'id_pipeline' => 2, "set_result" => Deal::RESULT_LOST, "probability" => 0 ]);

      $idPipeline = $mPipeline->record->recordCreate([ "name" => "Default pipeline" ])['id'];
      $mPipelineStep->record->recordCreate([ 'name' => 'Prospecting', 'order' => 1, 'color' => '#4080A0', 'id_pipeline' => $idPipeline , "set_result" => Deal::RESULT_PENDING, "probability" => 1]);
      $mPipelineStep->record->recordCreate([ 'name' => 'Qualified to buy', 'order' => 2, 'color' => '#A04020', 'id_pipeline' => $idPipeline, "set_result" => Deal::RESULT_PENDING, "probability" => 10]);
      $mPipelineStep->record->recordCreate([ 'name' => 'Proposal & Quote Sent', 'order' => 3, 'color' => '#006060', 'id_pipeline' => $idPipeline, "set_result" => Deal::RESULT_PENDING , "probability" => 30]);
      $mPipelineStep->record->recordCreate([ 'name' => 'Negotiation & Adjustments', 'order' => 4, 'color' => '#006060', 'id_pipeline' => $idPipeline, "set_result" => Deal::RESULT_PENDING , "probability" => 50]);
      $mPipelineStep->record->recordCreate([ 'name' => 'Decision-Making Phase', 'order' => 5, 'color' => '#006060', 'id_pipeline' => $idPipeline, "set_result" => Deal::RESULT_PENDING , "probability" => 70]);
      $mPipelineStep->record->recordCreate([ 'name' => 'Contract Sent', 'order' => 6, 'color' => '#002080', 'id_pipeline' => $idPipeline, "set_result" => Deal::RESULT_PENDING , "probability" => 85]);
      $mPipelineStep->record->recordCreate([ 'name' => 'Closed WON', 'order' => 6, 'color' => '#008000', 'id_pipeline' => $idPipeline, "set_result" => Deal::RESULT_WON , "probability" => 100]);
      $mPipelineStep->record->recordCreate([ 'name' => 'Closed LOST', 'order' => 4, 'color' => '#f50c0c', 'id_pipeline' => $idPipeline, "set_result" => Deal::RESULT_LOST , "probability" => 0]);
    }
  }

  public function installDefaultPermissions(): void
  {
    $mPermission = new \HubletoApp\Community\Settings\Models\Permission($this->main);
    $permissions = [
      "HubletoApp/Community/Pipeline/Controllers/Home",

      "HubletoApp/Community/Pipeline/Home",

      "HubletoApp/Community/Pipeline/Models/Pipeline:Create",
      "HubletoApp/Community/Pipeline/Models/Pipeline:Read",
      "HubletoApp/Community/Pipeline/Models/Pipeline:Update",
      "HubletoApp/Community/Pipeline/Models/Pipeline:Delete",

      "HubletoApp/Community/Pipeline/Models/PipelineStep:Create",
      "HubletoApp/Community/Pipeline/Models/PipelineStep:Read",
      "HubletoApp/Community/Pipeline/Models/PipelineStep:Update",
      "HubletoApp/Community/Pipeline/Models/PipelineStep:Delete",

      "HubletoApp/Community/Pipeline/Controllers/Pipeline",
      "HubletoApp/Community/Pipeline/Controllers/PipelineStep",

      "HubletoApp/Community/Pipeline/Pipeline",
      "HubletoApp/Community/Pipeline/PipelineStep",
    ];

    foreach ($permissions as $permission) {
      $mPermission->record->recordCreate([
        "permission" => $permission
      ]);
    }
  }
}