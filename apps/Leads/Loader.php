<?php

namespace Hubleto\App\Community\Leads;

class Loader extends \Hubleto\Framework\App
{
  public bool $hasCustomSettings = true;

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
      '/^leads\/api\/move-to-archive\/?$/' => Controllers\Api\MoveToArchive::class,
      '/^leads\/api\/log-activity\/?$/' => Controllers\Api\LogActivity::class,
      '/^leads\/api\/save-bulk-status-change\/?$/' => Controllers\Api\SaveBulkStatusChange::class,

      '/^leads\/boards\/lead-value-by-score\/?$/' => Controllers\Boards\LeadValueByScore::class,
      '/^leads\/boards\/lead-warnings\/?$/' => Controllers\Boards\LeadWarnings::class,

      '/^leads(\/(?<recordId>\d+))?\/?$/' => Controllers\Leads::class,
      '/^leads\/add?\/?$/' => ['controller' => Controllers\Leads::class, 'vars' => [ 'recordId' => -1 ]],
      '/^leads\/settings\/?$/' => Controllers\Settings::class,

      '/^leads\/tags\/?$/' => Controllers\Tags::class,
      '/^leads\/levels\/?$/' => Controllers\Levels::class,
      '/^leads\/lost-reasons\/?$/' => Controllers\LostReasons::class,
    ]);

    $this->addSearchSwitch('l', 'leads');
    $this->addSearchSwitch('t', 'taskleads');

    $settingsApp = $this->appManager()->getApp(\Hubleto\App\Community\Settings\Loader::class);
    $settingsApp->addSetting($this, [
      'title' => $this->translate('Lead Levels'),
      'icon' => 'fas fa-layer-group',
      'url' => 'leads/levels',
    ]);
    $settingsApp->addSetting($this, [
      'title' => $this->translate('Lead Tags'),
      'icon' => 'fas fa-tags',
      'url' => 'leads/levels',
    ]);
    $settingsApp->addSetting($this, [
      'title' => $this->translate('Lead Lost Reasons'),
      'icon' => 'fas fa-tags',
      'url' => 'leads/lost-reasons',
    ]);

    /** @var \Hubleto\App\Community\Calendar\Manager */
    $calendarManager = $this->getService(\Hubleto\App\Community\Calendar\Manager::class);
    $calendarManager->addCalendar($this, 'leads', $this->configAsString('calendarColor'), Calendar::class);

    /** @var \Hubleto\App\Community\Workflow\Manager */
    $workflowManager = $this->getService(\Hubleto\App\Community\Workflow\Manager::class);
    $workflowManager->addWorkflow($this, 'leads', Workflow::class);

    /** @var \Hubleto\App\Community\Dashboards\Manager */
    $boards = $this->getService(\Hubleto\App\Community\Dashboards\Manager::class);
    $boards->addBoard( $this, 'Lead value by score', 'leads/boards/lead-value-by-score');
    $boards->addBoard( $this, 'Lead warnings', 'leads/boards/lead-warnings');

    /** @var \Hubleto\App\Community\Desktop\AppMenuManager */
    $appMenu = $this->getService(\Hubleto\App\Community\Desktop\AppMenuManager::class);
    $appMenu->addItem($this, 'leads', $this->translate('Active leads'), 'fas fa-people-arrows');
    $appMenu->addItem($this, 'leads/archive', $this->translate('Archived leads'), 'fas fa-box-archive');
  }

  public function installTables(int $round): void
  {
    if ($round == 1) {
      $mLevel = $this->getModel(Models\Level::class);
      $mLead = $this->getModel(Models\Lead::class);
      $mLeadHistory = $this->getModel(Models\LeadHistory::class);
      $mLeadTag = $this->getModel(Models\Tag::class);
      $mCrossLeadTag = $this->getModel(Models\LeadTag::class);
      $mLeadTask = $this->getModel(Models\LeadTask::class);
      $mLeadCampaign = $this->getModel(Models\LeadCampaign::class);
      $mLeadActivity = $this->getModel(Models\LeadActivity::class);
      $mLeadDocument = $this->getModel(Models\LeadDocument::class);
      $mLostReasons = $this->getModel(Models\LostReason::class);

      $mLevel->dropTableIfExists()->install();
      $mLostReasons->dropTableIfExists()->install();
      $mLead->dropTableIfExists()->install();
      $mLeadHistory->dropTableIfExists()->install();
      $mLeadTag->dropTableIfExists()->install();
      $mCrossLeadTag->dropTableIfExists()->install();
      $mLeadActivity->dropTableIfExists()->install();
      $mLeadDocument->dropTableIfExists()->install();
      $mLeadCampaign->dropTableIfExists()->install();
      $mLeadTask->dropTableIfExists()->install();

      $mLeadTag->record->recordCreate([ 'name' => "Complex", 'color' => '#2196f3' ]);
      $mLeadTag->record->recordCreate([ 'name' => "Great opportunity", 'color' => '#4caf50' ]);
      $mLeadTag->record->recordCreate([ 'name' => "Duplicate", 'color' => '#9e9e9e' ]);
      $mLeadTag->record->recordCreate([ 'name' => "Needs attention", 'color' => '#795548' ]);

      $mLevel->record->recordCreate([ 'name' => "Cold", 'color' => '#2196f3' ]);
      $mLevel->record->recordCreate([ 'name' => "Warm", 'color' => '#4caf50' ]);
      $mLevel->record->recordCreate([ 'name' => "Hot", 'color' => '#9e9e9e' ]);
      $mLevel->record->recordCreate([ 'name' => "Marketing qualified", 'color' => '#795548' ]);
      $mLevel->record->recordCreate([ 'name' => "Sales qualified", 'color' => '#795548' ]);

      $mLostReasons->record->recordCreate(["reason" => "Price"]);
      $mLostReasons->record->recordCreate(["reason" => "Solution"]);
      $mLostReasons->record->recordCreate(["reason" => "Demand canceled by customer"]);
      $mLostReasons->record->recordCreate(["reason" => "Other"]);
    }
  }

  /**
   * Implements fulltext search functionality for tasks
   *
   * @param array $expressions List of expressions to be searched and glued with logical 'or'.
   * 
   * @return array
   * 
   */
  public function search(array $expressions): array
  {
    $mLead = $this->getModel(Models\Lead::class);
    $qLeads = $mLead->record->prepareReadQuery();
    
    foreach ($expressions as $e) {
      $qLeads = $qLeads->where(function($q) use ($e) {
        $q->orWhere('leads.id', 'like', '%' . $e . '%');
        $q->orWhere('leads.title', 'like', '%' . $e . '%');
      })
      ->where('leads.is_closed', false);
    }

    $leads = $qLeads->get()->toArray();

    $results = [];

    foreach ($leads as $lead) {
      $results[] = [
        "id" => $lead['id'],
        "label" => $lead['id'] . ' ' . $lead['title'],
        "url" => 'leads/' . $lead['id'],
        // "description" => $task[''],
      ];
    }

    return $results;
  }

}
