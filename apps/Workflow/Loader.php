<?php

namespace Hubleto\App\Community\Workflow;

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
      '/^workflow\/api\/get-workflows\/?$/' => Controllers\Api\GetWorkflows::class,
      '/^workflow\/api\/get-workflow-step-by-tag\/?$/' => Controllers\Api\GetWorkflowStepByTag::class,

      '/^workflow\/boards\/items-with-not-updated-step\/?$/' => Controllers\Boards\ItemsWithNotUpdatedStep::class,

      '/^workflow\/?$/' => Controllers\Workflow::class,
      '/^workflow(\/(?<idWorkflow>\d+))?\/?$/' => Controllers\Workflow::class,
      '/^workflow\/history\/?$/' => Controllers\History::class,
      '/^settings\/workflows\/?$/' => Controllers\Workflows::class,
    ]);

    $settingsApp = $this->appManager()->getApp(\Hubleto\App\Community\Settings\Loader::class);
    $settingsApp->addSetting($this, [
      'title' => $this->translate('Workflows'),
      'icon' => 'fas fa-bars-progress',
      'url' => 'settings/workflows'
    ]);

    /** @var \Hubleto\App\Community\Dashboards\Manager */
    $dashboardManager = $this->getService(\Hubleto\App\Community\Dashboards\Manager::class);
    $dashboardManager->addBoard(
      $this,
      $this->translate('Items with not updated step'),
      'workflow/boards/items-with-not-updated-step'
    );
  }

  /**
   * [Description for renderSecondSidebar]
   *
   * @return string
   * 
   */
  public function renderSecondSidebar(): string
  {
    $mWorkflow = $this->getModel(Models\Workflow::class);

    $html = '';
    foreach ($mWorkflow->record->where('show_in_kanban', true)->orderBy('order')->get() as $workflow) {
      $html .= '
        <a
          class="
            btn ' . ($workflow->id == $this->router()->urlParamAsInteger('idWorkflow') ? "btn-active" : "btn-transparent") . '
            mb-2 w-full
          "
          href="' . $this->env()->projectUrl . '/workflow/' . $workflow->id . '"
        >
          <span class="text">' . $workflow->name . '</span>
        </a>
      ';
    }
    $html .= '
      <a
        class="btn btn-transparent mt-2"
        href="' . $this->env()->projectUrl . '/settings/workflows"
      >
        <span class="icon"><i class="fas fa-cog"></i></span>
        <span class="text">Manage workflows</span>
      </a>
    ';

    return $html;
  }

  /**
   * [Description for installTables]
   *
   * @param int $round
   * 
   * @return void
   * 
   */
  public function installTables(int $round): void
  {
    if ($round == 1) {
      $mWorkflow = $this->getModel(Models\Workflow::class);
      $mWorkflowStep = $this->getModel(Models\WorkflowStep::class);
      $mWorkflowHistory = $this->getModel(Models\WorkflowHistory::class);

      $mWorkflow->dropTableIfExists()->install();
      $mWorkflowStep->dropTableIfExists()->install();
      $mWorkflowHistory->dropTableIfExists()->install();

      $idWorkflow = $mWorkflow->record->recordCreate([ "name" => "Campaigns", "order" => 1, "group" => "campaigns" ])['id'];
      $mWorkflowStep->record->recordCreate(['id_workflow' => $idWorkflow, 'name' => 'Preparation', 'order' => 1, 'color' => '#344556', 'tag' => 'campaign-preparation']);
      $mWorkflowStep->record->recordCreate(['id_workflow' => $idWorkflow, 'name' => 'Launched', 'order' => 2, 'color' => '#6830a5', 'tag' => 'campaign-launched']);
      $mWorkflowStep->record->recordCreate(['id_workflow' => $idWorkflow, 'name' => 'Analysis', 'order' => 3, 'color' => '#008000', 'tag' => 'campaign-analysis']);

      $idWorkflow = $mWorkflow->record->recordCreate([ "name" => "Leads", "order" => 2, "group" => "leads" ])['id'];
      $mWorkflowStep->record->recordCreate(['id_workflow' => $idWorkflow, 'name' => 'Subscriber', 'order' => 1, 'color' => '#344556', 'tag' => 'lead-subscriber']);
      $mWorkflowStep->record->recordCreate(['id_workflow' => $idWorkflow, 'name' => 'MQL', 'order' => 2, 'color' => '#344556', 'tag' => 'lead-mql']);
      $mWorkflowStep->record->recordCreate(['id_workflow' => $idWorkflow, 'name' => 'SQL', 'order' => 3, 'color' => '#6830a5', 'tag' => 'lead-sql']);
      $mWorkflowStep->record->recordCreate(['id_workflow' => $idWorkflow, 'name' => 'Opportunity', 'order' => 4, 'color' => '#3068a5', 'tag' => 'lead-opportunity']);
      $mWorkflowStep->record->recordCreate(['id_workflow' => $idWorkflow, 'name' => 'Customer', 'order' => 5, 'color' => '#ae459f', 'tag' => 'lead-customer']);

      $idWorkflow = $mWorkflow->record->recordCreate([ "name" => "Deals", "order" => 3, "group" => "deals" ])['id'];
      $mWorkflowStep->record->recordCreate([ 'name' => 'Prospecting', 'order' => 1, 'color' => '#838383', 'id_workflow' => $idWorkflow , "set_result" => Deal::RESULT_UNKNOWN, "probability" => 1, 'tag' => 'deal-prospecting']);
      $mWorkflowStep->record->recordCreate([ 'name' => 'Qualified', 'order' => 2, 'color' => '#d8a082', 'id_workflow' => $idWorkflow, "set_result" => Deal::RESULT_UNKNOWN, "probability" => 10, 'tag' => 'deal-qualified']);
      $mWorkflowStep->record->recordCreate([ 'name' => 'Quote Sent', 'order' => 3, 'color' => '#d1cf79', 'id_workflow' => $idWorkflow, "set_result" => Deal::RESULT_UNKNOWN , "probability" => 30, 'tag' => 'deal-quote-sent']);
      $mWorkflowStep->record->recordCreate([ 'name' => 'Under Review', 'order' => 5, 'color' => '#82b3d8', 'id_workflow' => $idWorkflow, "set_result" => Deal::RESULT_UNKNOWN , "probability" => 70, 'tag' => 'deal-under-review']);
      $mWorkflowStep->record->recordCreate([ 'name' => 'Contracting', 'order' => 6, 'color' => '#82d88b', 'id_workflow' => $idWorkflow, "set_result" => Deal::RESULT_UNKNOWN , "probability" => 85, 'tag' => 'deal-contracting']);
      $mWorkflowStep->record->recordCreate([ 'name' => 'WON', 'order' => 7, 'color' => '#008000', 'id_workflow' => $idWorkflow, "set_result" => Deal::RESULT_WON , "probability" => 100, 'tag' => 'deal-wON']);
      $mWorkflowStep->record->recordCreate([ 'name' => 'LOST', 'order' => 8, 'color' => '#f50c0c', 'id_workflow' => $idWorkflow, "set_result" => Deal::RESULT_LOST , "probability" => 0, 'tag' => 'deal-lOST']);

      $idWorkflow = $mWorkflow->record->recordCreate([ "name" => "Orders", "order" => 4, "group" => "orders" ])['id'];
      $mWorkflowStep->record->recordCreate(['id_workflow' => $idWorkflow, 'name' => 'In progress', 'order' => 1, 'color' => '#344556', 'tag' => 'order-in-progress']);
      $mWorkflowStep->record->recordCreate(['id_workflow' => $idWorkflow, 'name' => 'Delivered', 'order' => 2, 'color' => '#6830a5', 'tag' => 'order-delivered']);
      $mWorkflowStep->record->recordCreate(['id_workflow' => $idWorkflow, 'name' => 'Ready to invoice', 'order' => 3, 'color' => '#3068a5', 'tag' => 'order-ready-to-invoice']);
      $mWorkflowStep->record->recordCreate(['id_workflow' => $idWorkflow, 'name' => 'Invoiced', 'order' => 4, 'color' => '#ae459f', 'tag' => 'order-invoiced']);
      $mWorkflowStep->record->recordCreate(['id_workflow' => $idWorkflow, 'name' => 'Paid', 'order' => 5, 'color' => '#a38f9a', 'tag' => 'order-paid']);

      $idWorkflow = $mWorkflow->record->recordCreate([ "name" => "Projects", "order" => 5, "group" => "projects" ])['id'];
      $mWorkflowStep->record->recordCreate(['id_workflow' => $idWorkflow, 'name' => 'Preparation', 'order' => 1, 'color' => '#344556', 'tag' => 'project-preparation']);
      $mWorkflowStep->record->recordCreate(['id_workflow' => $idWorkflow, 'name' => 'Implementation', 'order' => 2, 'color' => '#d8a082', 'tag' => 'project-implementation']);
      $mWorkflowStep->record->recordCreate(['id_workflow' => $idWorkflow, 'name' => 'Testing', 'order' => 3, 'color' => '#6830a5', 'tag' => 'project-testing']);
      $mWorkflowStep->record->recordCreate(['id_workflow' => $idWorkflow, 'name' => 'Delivered', 'order' => 4, 'color' => '#008000', 'tag' => 'project-delivered']);

      $idWorkflow = $mWorkflow->record->recordCreate([ "name" => "Tasks", "order" => 6, "group" => "tasks" ])['id'];
      $mWorkflowStep->record->recordCreate(['id_workflow' => $idWorkflow, 'name' => 'In progress', 'order' => 1, 'color' => '#344556', 'tag' => 'task-in-progress']);
      $mWorkflowStep->record->recordCreate(['id_workflow' => $idWorkflow, 'name' => 'Ready to test', 'order' => 2, 'color' => '#6830a5', 'tag' => 'task-ready-to-test']);
      $mWorkflowStep->record->recordCreate(['id_workflow' => $idWorkflow, 'name' => 'Test passed', 'order' => 3, 'color' => '#3068a5', 'tag' => 'task-test-passed']);
      $mWorkflowStep->record->recordCreate(['id_workflow' => $idWorkflow, 'name' => 'Test failed', 'order' => 4, 'color' => '#ae459f', 'tag' => 'task-test-failed']);
      $mWorkflowStep->record->recordCreate(['id_workflow' => $idWorkflow, 'name' => 'Ready to deploy', 'order' => 5, 'color' => '#a38f9a', 'tag' => 'task-ready-to-deploy']);
      $mWorkflowStep->record->recordCreate(['id_workflow' => $idWorkflow, 'name' => 'Deployed', 'order' => 6, 'color' => '#44879a', 'tag' => 'task-deployed']);
      $mWorkflowStep->record->recordCreate(['id_workflow' => $idWorkflow, 'name' => 'Accepted', 'order' => 7, 'color' => '#74809a', 'tag' => 'task-accepted']);

      $idWorkflow = $mWorkflow->record->recordCreate([ "name" => "Invoices", "order" => 7, "group" => "invoices" ])['id'];
      $mWorkflowStep->record->recordCreate(['id_workflow' => $idWorkflow, 'name' => 'Issued', 'order' => 1, 'color' => '#344556', 'tag' => 'invoice-issued']);
      $mWorkflowStep->record->recordCreate(['id_workflow' => $idWorkflow, 'name' => 'Sent', 'order' => 2, 'color' => '#6830a5', 'tag' => 'invoice-sent']);
      $mWorkflowStep->record->recordCreate(['id_workflow' => $idWorkflow, 'name' => 'Paid', 'order' => 3, 'color' => '#008000', 'tag' => 'invoice-paid']);
    }
  }

}
