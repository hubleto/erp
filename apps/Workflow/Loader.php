<?php

namespace Hubleto\App\Community\Workflow;

use Hubleto\App\Community\Deals\Models\Deal;
use Hubleto\App\Community\Orders\Models\Order;
use Hubleto\App\Community\Workflow\Automats\Actions\SetWorkflow;
use Hubleto\App\Community\Workflow\Automats\Actions\UpdateRecord;
use Hubleto\App\Community\Workflow\Automats\Evaluators\RecordCompare;
use Hubleto\App\Community\Workflow\Automats\Evaluators\WorkflowCompare;
use Hubleto\App\Community\Workflow\Models\WorkflowStep;

class Loader extends \Hubleto\Erp\App
{

  /**
   * Inits the app: adds routes, settings, calendars, event listeners, menu items, ...
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

      '/^workflow\/automats(\/(?<recordId>\d+))?\/?$/' => Controllers\Automats::class,
      '/^workflow\/automats\/add\/?$/' => ['controller' => Controllers\Automats::class, 'vars' => ['recordId' => -1]],

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

    $this->eventManager()->addEventListener(
      'onModelAfterUpdate',
      $this->getService(EventListeners\SaveWorkflowHistory::class)
    );

    $this->eventManager()->addEventListener(
      'onModelAfterUpdate',
      $this->getService(EventListeners\WorkflowAutomat::class)
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

    $workflowButtonsHtml = '';
    foreach ($mWorkflow->record->where('show_in_kanban', true)->orderBy('order')->get() as $workflow) {
      $workflowButtonsHtml .= '
        <a
          class="
            btn btn-small ' . ($workflow->id == $this->router()->urlParamAsInteger('idWorkflow') ? "btn-active" : "btn-transparent") . '
          "
          href="' . $this->env()->projectUrl . '/workflow/' . $workflow->id . '"
        >
          <span class="icon"><i class="fas fa-bars-progress"></i></span>
          <span class="text">' . $workflow->name . '</span>
        </a>
      ';
    }

    return '
      <div class="flex flex-col gap-2">
        <a class="btn btn-square btn-primary-outline" href="' . $this->env()->projectUrl . '/workflow">
          <span class="icon"><i class="fas fa-timeline"></i></span>
          <span class="text">' . $this->translate('Workflow') . '</span>
        </a>
        ' . $workflowButtonsHtml . '
        <a class="btn btn-transparent" href="' . $this->env()->projectUrl . '/settings/workflows">
          <span class="icon"><i class="fas fa-cog"></i></span>
          <span class="text">' . $this->translate('Workflows') . '</span>
        </a>
        <a class="btn btn-transparent" href="' . $this->env()->projectUrl . '/workflow/automats">
          <span class="icon"><i class="fas fa-robot"></i></span>
          <span class="text">' . $this->translate('Automats') . '</span>
        </a>
      </div>
    ';
  }

  /**
   * [Description for upgradeSchema]
   *
   * @param int $round
   * 
   * @return void
   * 
   */
  public function installApp(int $round): void
  {
    if ($round == 1) {
      $mWorkflow = $this->getModel(Models\Workflow::class);
      $mWorkflowStep = $this->getModel(Models\WorkflowStep::class);
      $mWorkflowHistory = $this->getModel(Models\WorkflowHistory::class);
      $mAutomat = $this->getModel(Models\Automat::class);

      $mWorkflow->upgradeSchema();
      $mWorkflowStep->upgradeSchema();
      $mWorkflowHistory->upgradeSchema();
      $mAutomat->upgradeSchema();

      $idWorkflow = $mWorkflow->record->recordCreate([ "name" => $this->translate('Campaigns'), "show_in_kanban" => 1, "order" => 1, "group" => "campaigns" ])['id'];
      $mWorkflowStep->record->recordCreate(['id_workflow' => $idWorkflow, 'name' => $this->translate('Preparation'), 'order' => 1, 'color' => '#344556', 'tag' => 'campaign-preparation']);
      $mWorkflowStep->record->recordCreate(['id_workflow' => $idWorkflow, 'name' => $this->translate('Launched'), 'order' => 2, 'color' => '#6830a5', 'tag' => 'campaign-launched']);
      $mWorkflowStep->record->recordCreate(['id_workflow' => $idWorkflow, 'name' => $this->translate('Analysis'), 'order' => 3, 'color' => '#008000', 'tag' => 'campaign-analysis']);

      $idWorkflow = $mWorkflow->record->recordCreate([ "name" => $this->translate('Leads'), "show_in_kanban" => 1, "order" => 2, "group" => "leads" ])['id'];
      $mWorkflowStep->record->recordCreate(['id_workflow' => $idWorkflow, 'name' => $this->translate('Subscriber'), 'order' => 1, 'color' => '#344556', 'tag' => 'lead-subscriber']);
      $mWorkflowStep->record->recordCreate(['id_workflow' => $idWorkflow, 'name' => $this->translate('MQL'), 'order' => 2, 'color' => '#344556', 'tag' => 'lead-mql']);
      $mWorkflowStep->record->recordCreate(['id_workflow' => $idWorkflow, 'name' => $this->translate('SQL'), 'order' => 3, 'color' => '#6830a5', 'tag' => 'lead-sql']);
      $mWorkflowStep->record->recordCreate(['id_workflow' => $idWorkflow, 'name' => $this->translate('Opportunity'), 'order' => 4, 'color' => '#3068a5', 'tag' => 'lead-opportunity']);
      $mWorkflowStep->record->recordCreate(['id_workflow' => $idWorkflow, 'name' => $this->translate('Customer'), 'order' => 5, 'color' => '#ae459f', 'tag' => 'lead-customer']);

      $idWorkflow = $mWorkflow->record->recordCreate([ "name" => $this->translate('Deals'), "show_in_kanban" => 1, "order" => 3, "group" => "deals" ])['id'];
      $mWorkflowStep->record->recordCreate([ 'name' => $this->translate('Prospecting'), 'order' => 1, 'color' => '#838383', 'id_workflow' => $idWorkflow, "set_result" => Deal::RESULT_UNKNOWN, "probability" => 1, 'tag' => 'deal-prospecting']);
      $mWorkflowStep->record->recordCreate([ 'name' => $this->translate('Qualified'), 'order' => 2, 'color' => '#d8a082', 'id_workflow' => $idWorkflow, "set_result" => Deal::RESULT_UNKNOWN, "probability" => 10, 'tag' => 'deal-qualified']);
      $mWorkflowStep->record->recordCreate([ 'name' => $this->translate('Quote Sent'), 'order' => 3, 'color' => '#d1cf79', 'id_workflow' => $idWorkflow, "set_result" => Deal::RESULT_UNKNOWN, "probability" => 30, 'tag' => 'deal-quote-sent']);
      $mWorkflowStep->record->recordCreate([ 'name' => $this->translate('Under Review'), 'order' => 5, 'color' => '#82b3d8', 'id_workflow' => $idWorkflow, "set_result" => Deal::RESULT_UNKNOWN, "probability" => 70, 'tag' => 'deal-under-review']);
      $mWorkflowStep->record->recordCreate([ 'name' => $this->translate('Contracting'), 'order' => 6, 'color' => '#82d88b', 'id_workflow' => $idWorkflow, "set_result" => Deal::RESULT_UNKNOWN, "probability" => 85, 'tag' => 'deal-contracting']);
      $mWorkflowStep->record->recordCreate([ 'name' => $this->translate('WON'), 'order' => 7, 'color' => '#008000', 'id_workflow' => $idWorkflow, "set_result" => Deal::RESULT_WON, "probability" => 100, 'tag' => 'deal-won']);
      $mWorkflowStep->record->recordCreate([ 'name' => $this->translate('LOST'), 'order' => 8, 'color' => '#f50c0c', 'id_workflow' => $idWorkflow, "set_result" => Deal::RESULT_LOST, "probability" => 0, 'tag' => 'deal-lost']);

      $idWorkflow = $mWorkflow->record->recordCreate([ "name" => $this->translate('Orders'), "show_in_kanban" => 1, "order" => 4, "group" => "orders" ])['id'];
      $mWorkflowStep->record->recordCreate(['id_workflow' => $idWorkflow, 'name' => $this->translate('In progress'), 'order' => 1, 'color' => '#344556', 'tag' => 'order-in-progress']);
      $mWorkflowStep->record->recordCreate(['id_workflow' => $idWorkflow, 'name' => $this->translate('Delivered'), 'order' => 2, 'color' => '#6830a5', 'tag' => 'order-delivered']);
      $mWorkflowStep->record->recordCreate(['id_workflow' => $idWorkflow, 'name' => $this->translate('Ready to invoice'), 'order' => 3, 'color' => '#3068a5', 'tag' => 'order-ready-to-invoice']);
      $mWorkflowStep->record->recordCreate(['id_workflow' => $idWorkflow, 'name' => $this->translate('Invoiced'), 'order' => 4, 'color' => '#ae459f', 'tag' => 'order-invoiced']);
      $mWorkflowStep->record->recordCreate(['id_workflow' => $idWorkflow, 'name' => $this->translate('Paid'), 'order' => 5, 'color' => '#a38f9a', 'tag' => 'order-paid']);

      $idWorkflow = $mWorkflow->record->recordCreate([ "name" => $this->translate('Projects'), "show_in_kanban" => 1, "order" => 5, "group" => "projects" ])['id'];
      $mWorkflowStep->record->recordCreate(['id_workflow' => $idWorkflow, 'name' => $this->translate('Preparation'), 'order' => 1, 'color' => '#344556', 'tag' => 'project-preparation']);
      $mWorkflowStep->record->recordCreate(['id_workflow' => $idWorkflow, 'name' => $this->translate('Implementation'), 'order' => 2, 'color' => '#d8a082', 'tag' => 'project-implementation']);
      $mWorkflowStep->record->recordCreate(['id_workflow' => $idWorkflow, 'name' => $this->translate('Testing'), 'order' => 3, 'color' => '#6830a5', 'tag' => 'project-testing']);
      $mWorkflowStep->record->recordCreate(['id_workflow' => $idWorkflow, 'name' => $this->translate('Delivered'), 'order' => 4, 'color' => '#008000', 'tag' => 'project-delivered']);

      $idWorkflow = $mWorkflow->record->recordCreate([ "name" => $this->translate('Tasks'), "show_in_kanban" => 1, "order" => 6, "group" => "tasks" ])['id'];
      $mWorkflowStep->record->recordCreate(['id_workflow' => $idWorkflow, 'name' => $this->translate('In progress'), 'order' => 1, 'color' => '#344556', 'tag' => 'task-in-progress']);
      $mWorkflowStep->record->recordCreate(['id_workflow' => $idWorkflow, 'name' => $this->translate('Ready to test'), 'order' => 2, 'color' => '#6830a5', 'tag' => 'task-ready-to-test']);
      $mWorkflowStep->record->recordCreate(['id_workflow' => $idWorkflow, 'name' => $this->translate('Test passed'), 'order' => 3, 'color' => '#3068a5', 'tag' => 'task-test-passed']);
      $mWorkflowStep->record->recordCreate(['id_workflow' => $idWorkflow, 'name' => $this->translate('Test failed'), 'order' => 4, 'color' => '#ae459f', 'tag' => 'task-test-failed']);
      $mWorkflowStep->record->recordCreate(['id_workflow' => $idWorkflow, 'name' => $this->translate('Ready to deploy'), 'order' => 5, 'color' => '#a38f9a', 'tag' => 'task-ready-to-deploy']);
      $mWorkflowStep->record->recordCreate(['id_workflow' => $idWorkflow, 'name' => $this->translate('Deployed'), 'order' => 6, 'color' => '#44879a', 'tag' => 'task-deployed']);
      $mWorkflowStep->record->recordCreate(['id_workflow' => $idWorkflow, 'name' => $this->translate('Accepted'), 'order' => 7, 'color' => '#74809a', 'tag' => 'task-accepted']);

      $idWorkflow = $mWorkflow->record->recordCreate([ "name" => $this->translate('Documents'), "show_in_kanban" => 0, "order" => 7, "group" => "documents" ])['id'];
      $mWorkflowStep->record->recordCreate(['id_workflow' => $idWorkflow, 'name' => $this->translate('New'), 'order' => 1, 'color' => '#344556', 'tag' => 'document-new']);
      $mWorkflowStep->record->recordCreate(['id_workflow' => $idWorkflow, 'name' => $this->translate('Under review'), 'order' => 2, 'color' => '#6830a5', 'tag' => 'document-under-review']);
      $mWorkflowStep->record->recordCreate(['id_workflow' => $idWorkflow, 'name' => $this->translate('Approved'), 'order' => 3, 'color' => '#3068a5', 'tag' => 'document-approved']);
      $mWorkflowStep->record->recordCreate(['id_workflow' => $idWorkflow, 'name' => $this->translate('Rejected'), 'order' => 4, 'color' => '#ae459f', 'tag' => 'document-rejected']);

      $mAutomat->record->recordCreate([
        'name' => 'setOrderWorkflowStepToPaid',
        'trigger' => 'onModelAfterUpdate',
        'conditions' => json_encode([
          [
            'evaluator' => RecordCompare::class,
            'arguments' => [
              'model' => Order::class,
              'column' => 'is_closed',
              'operator' => '=',
              'value' => 1,
            ],
          ],
        ]),
        'actions' => json_encode([
          [
            'action' => SetWorkflow::class,
            'arguments' => [
              'tag' => 'order-paid',
            ]
          ],
        ]),
      ]);

      $mAutomat->record->recordCreate([
        'name' => 'setDealClosedIfWon',
        'trigger' => 'onModelAfterUpdate',
        'conditions' => json_encode([
          [ 'evaluator' => WorkflowCompare::class, 'arguments' => [ 'model' => Deal::class, 'tagIs' => 'deal-won' ] ],
        ]),
        'actions' => json_encode([
          [ 'action' => UpdateRecord::class, 'arguments' => [ 'column' => 'is_closed', 'value' => 1 ] ],
        ]),
      ]);

      $mAutomat->record->recordCreate([
        'name' => 'setDealClosedIfLost',
        'trigger' => 'onModelAfterUpdate',
        'conditions' => json_encode([
          [ 'evaluator' => WorkflowCompare::class, 'arguments' => [ 'model' => Deal::class, 'tagIs' => 'deal-lost' ] ],
        ]),
        'actions' => json_encode([
          [ 'action' => UpdateRecord::class, 'arguments' => [ 'column' => 'is_closed', 'value' => 1 ] ],
        ]),
      ]);

      $mAutomat->record->recordCreate([
        'name' => 'setDealClosed',
        'trigger' => 'onModelAfterUpdate',
        'conditions' => json_encode([
          [ 'evaluator' => WorkflowCompare::class, 'arguments' => [ 'model' => Deal::class, 'tagIsNot' => 'deal-won' ] ],
          [ 'evaluator' => WorkflowCompare::class, 'arguments' => [ 'model' => Deal::class, 'tagIsNot' => 'deal-lost' ] ],
        ]),
        'actions' => json_encode([
          [ 'action' => UpdateRecord::class, 'arguments' => [ 'column' => 'is_closed', 'value' => 0 ] ],
        ]),
      ]);
    }
  }

}
