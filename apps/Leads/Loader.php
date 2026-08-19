<?php

namespace Hubleto\App\Community\Leads;

class Loader extends \Hubleto\Erp\App
{

  private int $openLeadsWithoutFuturePlan = 0;

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
      '/^leads\/api\/log-activity\/?$/' => Controllers\Api\LogActivity::class,
      '/^leads\/api\/save-bulk-status-change\/?$/' => Controllers\Api\SaveBulkStatusChange::class,

      '/^leads\/boards\/lead-value-by-score\/?$/' => Controllers\Boards\LeadValueByScore::class,
      '/^leads\/boards\/lead-warnings\/?$/' => Controllers\Boards\LeadWarnings::class,

      '/^leads(\/(?<recordId>\d+))?\/?$/' => Controllers\Leads::class,
      '/^leads\/add?\/?$/' => ['controller' => Controllers\Leads::class, 'vars' => [ 'recordId' => -1 ]],
      '/^leads\/settings\/?$/' => Controllers\Settings::class,

      '/^leads\/tags\/?$/' => Controllers\Tags::class,
      '/^leads\/lost-reasons\/?$/' => Controllers\LostReasons::class,

      '/^leads\/plan\/?$/' => Controllers\Plan::class,
    ]);

    $settingsApp = $this->appManager()->getApp(\Hubleto\App\Community\Settings\Loader::class);
    $settingsApp->addSetting($this, [
      'title' => $this->translate('Lead Tags'),
      'icon' => 'fas fa-tags',
      'url' => 'leads/tags',
    ]);
    $settingsApp->addSetting($this, [
      'title' => $this->translate('Lead Lost Reasons'),
      'icon' => 'fas fa-tags',
      'url' => 'leads/lost-reasons',
    ]);

    /** @var \Hubleto\App\Community\Calendar\Manager */
    $calendarManager = $this->getService(\Hubleto\App\Community\Calendar\Manager::class);
    $calendarManager->addCalendar($this, 'leads', Calendar::class);

    /** @var \Hubleto\App\Community\Workflow\Manager */
    $workflowManager = $this->getService(\Hubleto\App\Community\Workflow\Manager::class);
    $workflowManager->addWorkflowGroup($this, 'leads', Workflow::class);

    /** @var \Hubleto\App\Community\Dashboards\Manager */
    $boards = $this->getService(\Hubleto\App\Community\Dashboards\Manager::class);
    $boards->addBoard( $this, $this->translate('Lead value by score'), 'leads/boards/lead-value-by-score');
    $boards->addBoard( $this, $this->translate('Lead warnings'), 'leads/boards/lead-warnings');

    /** @var \Hubleto\App\Community\Desktop\AppMenuManager */
    $appMenu = $this->getService(\Hubleto\App\Community\Desktop\AppMenuManager::class);
    $appMenu->addItem($this, 'leads', $this->translate('Active leads'), 'fas fa-people-arrows');
    $appMenu->addItem($this, 'leads/archive', $this->translate('Archived leads'), 'fas fa-box-archive');

    /** @var Counter */
    $counter = $this->getService(Counter::class);
    $this->openLeadsWithoutFuturePlan = $counter->openLeadsWithoutFuturePlan();
  }

  public function installApp(int $round): void
  {
    if ($round == 1) {
      $mLead = $this->getModel(Models\Lead::class);
      $mLeadHistory = $this->getModel(Models\LeadHistory::class);
      $mLeadTag = $this->getModel(Models\Tag::class);
      $mCrossLeadTag = $this->getModel(Models\LeadTag::class);
      $mLeadTask = $this->getModel(Models\LeadTask::class);
      $mLeadActivity = $this->getModel(Models\LeadActivity::class);
      $mLeadDocument = $this->getModel(Models\LeadDocument::class);
      $mLostReasons = $this->getModel(Models\LostReason::class);

      $mLostReasons->upgradeSchema();
      $mLead->upgradeSchema();
      $mLeadHistory->upgradeSchema();
      $mLeadTag->upgradeSchema();
      $mCrossLeadTag->upgradeSchema();
      $mLeadActivity->upgradeSchema();
      $mLeadDocument->upgradeSchema();
      $mLeadTask->upgradeSchema();

      $mLeadTag->record->recordCreate([ 'name' => $this->translate("Complex"), 'color' => '#2196f3' ]);
      $mLeadTag->record->recordCreate([ 'name' => $this->translate("Great opportunity"), 'color' => '#4caf50' ]);
      $mLeadTag->record->recordCreate([ 'name' => $this->translate("Duplicate"), 'color' => '#9e9e9e' ]);
      $mLeadTag->record->recordCreate([ 'name' => $this->translate("Needs attention"), 'color' => '#795548' ]);

      $mLostReasons->record->recordCreate(["reason" => $this->translate("Price")]);
      $mLostReasons->record->recordCreate(["reason" => $this->translate("Solution")]);
      $mLostReasons->record->recordCreate(["reason" => $this->translate("Demand canceled by customer")]);
      $mLostReasons->record->recordCreate(["reason" => $this->translate("Other")]);
    }
  }

  /**
   * [Description for getSidebarBadgeNumber]
   *
   * @return int
   *
   */
  public function getSidebarBadgeNumber(): int
  {
    return $this->openLeadsWithoutFuturePlan;
  }
  
  public function renderPriorityNotifications(): string
  {
    return 
      ''
      . ($this->openLeadsWithoutFuturePlan > 0 ? '
          <a
            href="' . $this->env()->projectUrl . '/leads?filters%5BfLeadClosed%5D=0&filters%5BfLeadWithPlan%5D=2"
          class="block badge badge-danger"
          >' . $this->openLeadsWithoutFuturePlan . ' ' . $this->translate('open leads without future plan') . '</a>
        ' : '')
    ;

  }

  /**
   * [Description for renderSecondSidebar]
   *
   * @return string
   *
   */
  public function renderSecondSidebar(): string
  {

    return '
      <div class="app-main-title"><a href="' . $this->env()->projectUrl . '/leads">
        ' . $this->translate('Leads') . '
      </a></div>
      <div class="app-sidebar-buttons">
        <a class="btn btn-transparent" href="' . $this->env()->projectUrl . '/leads/plan">
          <span class="icon"><i class="fas fa-list-ol"></i></span>
          <span class="text">' . $this->translate('Plan') . '</span>
        </a>
        <a class="btn btn-transparent" href="' . $this->env()->projectUrl . '/calendar?show=leads">
          <span class="icon"><i class="fas fa-calendar-days"></i></span>
          <span class="text">' . $this->translate('Calendar') . '</span>
        </a>
      </div>
    ';
  }

}
