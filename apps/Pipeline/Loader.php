<?php

namespace Hubleto\App\Community\Pipeline;

use Hubleto\App\Community\Deals\Models\Deal;

class Loader extends \Hubleto\Framework\App
{

  /**
   * Inits the app: adds routes, settings, calendars, hooks, menu items, ...
   *
   * @return void
   * 
   */
  public function init(): void
  {
    parent::init();

    $this->router()->get([
      '/^pipeline\/api\/get-pipelines\/?$/' => Controllers\Api\GetPipelines::class,
      '/^pipeline\/api\/get-pipeline-step-by-tag\/?$/' => Controllers\Api\GetPipelineStepByTag::class,
      '/^pipeline\/?$/' => Controllers\Pipeline::class,
      '/^pipeline(\/(?<idPipeline>\d+))?\/?$/' => Controllers\Pipeline::class,
      '/^pipeline\/history\/?$/' => Controllers\PipelineHistory::class,
      '/^settings\/pipelines\/?$/' => Controllers\Pipelines::class,
    ]);

    $settingsApp = $this->appManager()->getApp(\Hubleto\App\Community\Settings\Loader::class);
    $settingsApp->addSetting($this, [
      'title' => $this->translate('Pipelines'),
      'icon' => 'fas fa-bars-progress',
      'url' => 'settings/pipelines'
    ]);
  }

  public function installTables(int $round): void
  {
    if ($round == 1) {
      $mPipeline = $this->getModel(Models\Pipeline::class);
      $mPipelineStep = $this->getModel(Models\PipelineStep::class);
      $mPipelineHistory = $this->getModel(Models\PipelineHistory::class);

      $mPipeline->dropTableIfExists()->install();
      $mPipelineStep->dropTableIfExists()->install();
      $mPipelineHistory->dropTableIfExists()->install();

      $idPipeline = $mPipeline->record->recordCreate([ "name" => "Deal stage", "group" => "deals" ])['id'];
      $mPipelineStep->record->recordCreate([ 'name' => 'Prospecting', 'order' => 1, 'color' => '#838383', 'id_pipeline' => $idPipeline , "set_result" => Deal::RESULT_UNKNOWN, "probability" => 1, 'tag' => 'deal-prospecting']);
      $mPipelineStep->record->recordCreate([ 'name' => 'Qualified', 'order' => 2, 'color' => '#d8a082', 'id_pipeline' => $idPipeline, "set_result" => Deal::RESULT_UNKNOWN, "probability" => 10, 'tag' => 'deal-qualified']);
      $mPipelineStep->record->recordCreate([ 'name' => 'Quote Sent', 'order' => 3, 'color' => '#d1cf79', 'id_pipeline' => $idPipeline, "set_result" => Deal::RESULT_UNKNOWN , "probability" => 30, 'tag' => 'deal-quote-sent']);
      $mPipelineStep->record->recordCreate([ 'name' => 'Under Review', 'order' => 5, 'color' => '#82b3d8', 'id_pipeline' => $idPipeline, "set_result" => Deal::RESULT_UNKNOWN , "probability" => 70, 'tag' => 'deal-under-review']);
      $mPipelineStep->record->recordCreate([ 'name' => 'Contracting', 'order' => 6, 'color' => '#82d88b', 'id_pipeline' => $idPipeline, "set_result" => Deal::RESULT_UNKNOWN , "probability" => 85, 'tag' => 'deal-contracting']);
      $mPipelineStep->record->recordCreate([ 'name' => 'WON', 'order' => 7, 'color' => '#008000', 'id_pipeline' => $idPipeline, "set_result" => Deal::RESULT_WON , "probability" => 100, 'tag' => 'deal-wON']);
      $mPipelineStep->record->recordCreate([ 'name' => 'LOST', 'order' => 8, 'color' => '#f50c0c', 'id_pipeline' => $idPipeline, "set_result" => Deal::RESULT_LOST , "probability" => 0, 'tag' => 'deal-lOST']);

      $idPipeline = $mPipeline->record->recordCreate([ "name" => "Project phase", "group" => "projects" ])['id'];
      $mPipelineStep->record->recordCreate(['id_pipeline' => $idPipeline, 'name' => 'Preparation', 'order' => 1, 'color' => '#344556', 'tag' => 'project-preparation']);
      $mPipelineStep->record->recordCreate(['id_pipeline' => $idPipeline, 'name' => 'Implementation', 'order' => 2, 'color' => '#d8a082', 'tag' => 'project-implementation']);
      $mPipelineStep->record->recordCreate(['id_pipeline' => $idPipeline, 'name' => 'Testing', 'order' => 3, 'color' => '#6830a5', 'tag' => 'project-testing']);
      $mPipelineStep->record->recordCreate(['id_pipeline' => $idPipeline, 'name' => 'Delivered', 'order' => 4, 'color' => '#008000', 'tag' => 'project-delivered']);

      $idPipeline = $mPipeline->record->recordCreate([ "name" => "Task status", "group" => "tasks" ])['id'];
      $mPipelineStep->record->recordCreate(['id_pipeline' => $idPipeline, 'name' => 'In progress', 'order' => 1, 'color' => '#344556', 'tag' => 'task-in-progress']);
      $mPipelineStep->record->recordCreate(['id_pipeline' => $idPipeline, 'name' => 'Ready to test', 'order' => 2, 'color' => '#6830a5', 'tag' => 'task-ready-to-test']);
      $mPipelineStep->record->recordCreate(['id_pipeline' => $idPipeline, 'name' => 'Test passed', 'order' => 3, 'color' => '#3068a5', 'tag' => 'task-test-passed']);
      $mPipelineStep->record->recordCreate(['id_pipeline' => $idPipeline, 'name' => 'Test failed', 'order' => 4, 'color' => '#ae459f', 'tag' => 'task-test-failed']);
      $mPipelineStep->record->recordCreate(['id_pipeline' => $idPipeline, 'name' => 'Ready to deploy', 'order' => 5, 'color' => '#a38f9a', 'tag' => 'task-ready-to-deploy']);
      $mPipelineStep->record->recordCreate(['id_pipeline' => $idPipeline, 'name' => 'Deployed', 'order' => 6, 'color' => '#44879a', 'tag' => 'task-deployed']);
      $mPipelineStep->record->recordCreate(['id_pipeline' => $idPipeline, 'name' => 'Accepted', 'order' => 7, 'color' => '#74809a', 'tag' => 'task-accepted']);

      $idPipeline = $mPipeline->record->recordCreate([ "name" => "Order stage", "group" => "orders" ])['id'];
      $mPipelineStep->record->recordCreate(['id_pipeline' => $idPipeline, 'name' => 'In progress', 'order' => 1, 'color' => '#344556', 'tag' => 'order-in-progress']);
      $mPipelineStep->record->recordCreate(['id_pipeline' => $idPipeline, 'name' => 'Delivered', 'order' => 2, 'color' => '#6830a5', 'tag' => 'order-delivered']);
      $mPipelineStep->record->recordCreate(['id_pipeline' => $idPipeline, 'name' => 'Ready to invoice', 'order' => 3, 'color' => '#3068a5', 'tag' => 'order-ready-to-invoice']);
      $mPipelineStep->record->recordCreate(['id_pipeline' => $idPipeline, 'name' => 'Invoiced', 'order' => 4, 'color' => '#ae459f', 'tag' => 'order-invoiced']);
      $mPipelineStep->record->recordCreate(['id_pipeline' => $idPipeline, 'name' => 'Paid', 'order' => 5, 'color' => '#a38f9a', 'tag' => 'order-paid']);

      $idPipeline = $mPipeline->record->recordCreate([ "name" => "Lead level", "group" => "leads" ])['id'];
      $mPipelineStep->record->recordCreate(['id_pipeline' => $idPipeline, 'name' => 'Subscriber', 'order' => 1, 'color' => '#344556', 'tag' => 'lead-subscriber']);
      $mPipelineStep->record->recordCreate(['id_pipeline' => $idPipeline, 'name' => 'MQL', 'order' => 2, 'color' => '#344556', 'tag' => 'lead-mql']);
      $mPipelineStep->record->recordCreate(['id_pipeline' => $idPipeline, 'name' => 'SQL', 'order' => 3, 'color' => '#6830a5', 'tag' => 'lead-sql']);
      $mPipelineStep->record->recordCreate(['id_pipeline' => $idPipeline, 'name' => 'Opportunity', 'order' => 4, 'color' => '#3068a5', 'tag' => 'lead-opportunity']);
      $mPipelineStep->record->recordCreate(['id_pipeline' => $idPipeline, 'name' => 'Customer', 'order' => 5, 'color' => '#ae459f', 'tag' => 'lead-customer']);

      $idPipeline = $mPipeline->record->recordCreate([ "name" => "Campaign phase", "group" => "campaigns" ])['id'];
      $mPipelineStep->record->recordCreate(['id_pipeline' => $idPipeline, 'name' => 'Preparation', 'order' => 1, 'color' => '#344556', 'tag' => 'campaign-preparation']);
      $mPipelineStep->record->recordCreate(['id_pipeline' => $idPipeline, 'name' => 'Launched', 'order' => 2, 'color' => '#6830a5', 'tag' => 'campaign-launched']);
      $mPipelineStep->record->recordCreate(['id_pipeline' => $idPipeline, 'name' => 'Analysis', 'order' => 3, 'color' => '#008000', 'tag' => 'campaign-analysis']);

      $idPipeline = $mPipeline->record->recordCreate([ "name" => "Invoice state", "group" => "invoices" ])['id'];
      $mPipelineStep->record->recordCreate(['id_pipeline' => $idPipeline, 'name' => 'Issued', 'order' => 1, 'color' => '#344556', 'tag' => 'invoice-issued']);
      $mPipelineStep->record->recordCreate(['id_pipeline' => $idPipeline, 'name' => 'Sent', 'order' => 2, 'color' => '#6830a5', 'tag' => 'invoice-sent']);
      $mPipelineStep->record->recordCreate(['id_pipeline' => $idPipeline, 'name' => 'Paid', 'order' => 3, 'color' => '#008000', 'tag' => 'invoice-paid']);
    }
  }

}
