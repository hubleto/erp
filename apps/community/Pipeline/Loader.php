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


    $this->main->apps->community('Settings')->addSetting($this, [
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

      $idPipeline = $mPipeline->record->recordCreate([ "name" => "Default pipeline" ])['id'];
      $mPipelineStep->record->recordCreate([ 'name' => 'Prospecting', 'order' => 1, 'color' => '#838383', 'id_pipeline' => $idPipeline , "set_result" => Deal::RESULT_UNKNOWN, "probability" => 1]);
      $mPipelineStep->record->recordCreate([ 'name' => 'Qualified to buy', 'order' => 2, 'color' => '#d8a082', 'id_pipeline' => $idPipeline, "set_result" => Deal::RESULT_UNKNOWN, "probability" => 10]);
      $mPipelineStep->record->recordCreate([ 'name' => 'Proposal & Quote Sent', 'order' => 3, 'color' => '#d1cf79', 'id_pipeline' => $idPipeline, "set_result" => Deal::RESULT_UNKNOWN , "probability" => 30]);
      $mPipelineStep->record->recordCreate([ 'name' => 'Negotiation & Adjustments', 'order' => 4, 'color' => '#79d1a5', 'id_pipeline' => $idPipeline, "set_result" => Deal::RESULT_UNKNOWN , "probability" => 50]);
      $mPipelineStep->record->recordCreate([ 'name' => 'Decision-Making Phase', 'order' => 5, 'color' => '#82b3d8', 'id_pipeline' => $idPipeline, "set_result" => Deal::RESULT_UNKNOWN , "probability" => 70]);
      $mPipelineStep->record->recordCreate([ 'name' => 'Contract Sent', 'order' => 6, 'color' => '#82d88b', 'id_pipeline' => $idPipeline, "set_result" => Deal::RESULT_UNKNOWN , "probability" => 85]);
      $mPipelineStep->record->recordCreate([ 'name' => 'Closed WON', 'order' => 7, 'color' => '#008000', 'id_pipeline' => $idPipeline, "set_result" => Deal::RESULT_WON , "probability" => 100]);
      $mPipelineStep->record->recordCreate([ 'name' => 'Closed LOST', 'order' => 8, 'color' => '#f50c0c', 'id_pipeline' => $idPipeline, "set_result" => Deal::RESULT_LOST , "probability" => 0]);
    }
  }

  // public function installDefaultPermissions(): void
  // {
  //   $mPermission = new \HubletoApp\Community\Settings\Models\Permission($this->main);
  //   $permissions = [
  //     "HubletoApp/Community/Pipeline/Controllers/Home",

  //     "HubletoApp/Community/Pipeline/Home",

  //     "HubletoApp/Community/Pipeline/Models/Pipeline:Create",
  //     "HubletoApp/Community/Pipeline/Models/Pipeline:Read",
  //     "HubletoApp/Community/Pipeline/Models/Pipeline:Update",
  //     "HubletoApp/Community/Pipeline/Models/Pipeline:Delete",

  //     "HubletoApp/Community/Pipeline/Models/PipelineStep:Create",
  //     "HubletoApp/Community/Pipeline/Models/PipelineStep:Read",
  //     "HubletoApp/Community/Pipeline/Models/PipelineStep:Update",
  //     "HubletoApp/Community/Pipeline/Models/PipelineStep:Delete",

  //     "HubletoApp/Community/Pipeline/Controllers/Pipeline",
  //     "HubletoApp/Community/Pipeline/Controllers/PipelineStep",

  //     "HubletoApp/Community/Pipeline/Pipeline",
  //     "HubletoApp/Community/Pipeline/PipelineStep",
  //   ];

  //   foreach ($permissions as $permission) {
  //     $mPermission->record->recordCreate([
  //       "permission" => $permission
  //     ]);
  //   }
  // }
}