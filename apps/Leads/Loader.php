<?php

namespace HubletoApp\Community\Leads;

class Loader extends \Hubleto\Framework\App
{
  public bool $hasCustomSettings = true;

  public function init(): void
  {
    parent::init();

    $this->main->router->httpGet([
      '/^leads(\/(?<recordId>\d+))?\/?$/' => Controllers\Leads::class,
      '/^leads\/settings\/?$/' => Controllers\Settings::class,
      '/^leads\/archive\/?$/' => Controllers\LeadsArchive::class,
      '/^leads\/api\/move-to-archive\/?$/' => Controllers\Api\MoveToArchive::class,
      '/^leads\/api\/log-activity\/?$/' => Controllers\Api\LogActivity::class,
      '/^settings\/lead-tags\/?$/' => Controllers\Tags::class,
      '/^settings\/lead-levels\/?$/' => Controllers\Levels::class,
      '/^settings\/lead-lost-reasons\/?$/' => Controllers\LostReasons::class,
      '/^leads\/boards\/lead-value-by-score\/?$/' => Controllers\Boards\LeadValueByScore::class,
      '/^leads\/boards\/lead-warnings\/?$/' => Controllers\Boards\LeadWarnings::class,
      '/^leads\/save-bulk-status-change\/?$/' => Controllers\Api\SaveBulkStatusChange::class,
    ]);

    $this->main->apps->community('Settings')->addSetting($this, [
      'title' => $this->translate('Lead Levels'),
      'icon' => 'fas fa-layer-group',
      'url' => 'settings/lead-levels',
    ]);
    $this->main->apps->community('Settings')->addSetting($this, [
      'title' => $this->translate('Lead Tags'),
      'icon' => 'fas fa-tags',
      'url' => 'settings/lead-levels',
    ]);
    $this->main->apps->community('Settings')->addSetting($this, [
      'title' => $this->translate('Lead Lost Reasons'),
      'icon' => 'fas fa-tags',
      'url' => 'settings/lead-lost-reasons',
    ]);

    $calendarManager = $this->main->apps->community('Calendar')->calendarManager;
    $calendarManager->addCalendar(
      'leads',
      $this->configAsString('calendarColor'),
      Calendar::class
    );

    $dashboardsApp = $this->main->apps->community('Dashboards');
    if ($dashboardsApp) {
      $dashboardsApp->addBoard(
        $this,
        'Lead value by score',
        'leads/boards/lead-value-by-score'
      );

      $dashboardsApp->addBoard(
        $this,
        'Lead warnings',
        'leads/boards/lead-warnings'
      );
    }

    $dashboard = $this->main->apps->community('Desktop')->dashboard;

    $this->main->apps->community('Help')->addContextHelpUrls('/^leads\/?$/', [
      'en' => 'en/apps/community/leads',
    ]);

    $appMenu = $this->main->apps->community('Desktop')->appMenu;
    $appMenu->addItem($this, 'leads', $this->translate('Active leads'), 'fas fa-people-arrows');
    $appMenu->addItem($this, 'leads/archive', $this->translate('Archived leads'), 'fas fa-box-archive');
  }

  public function installTables(int $round): void
  {
    if ($round == 1) {
      $mLevel = $this->main->di->create(\HubletoApp\Community\Leads\Models\Level::class);
      $mLead = $this->main->di->create(\HubletoApp\Community\Leads\Models\Lead::class);
      $mLeadHistory = $this->main->di->create(\HubletoApp\Community\Leads\Models\LeadHistory::class);
      $mLeadTag = $this->main->di->create(\HubletoApp\Community\Leads\Models\Tag::class);
      $mCrossLeadTag = $this->main->di->create(\HubletoApp\Community\Leads\Models\LeadTag::class);
      $mLeadActivity = $this->main->di->create(\HubletoApp\Community\Leads\Models\LeadActivity::class);
      $mLeadDocument = $this->main->di->create(\HubletoApp\Community\Leads\Models\LeadDocument::class);
      $mLostReasons = $this->main->di->create(\HubletoApp\Community\Leads\Models\LostReason::class);

      $mLevel->dropTableIfExists()->install();
      $mLostReasons->dropTableIfExists()->install();
      $mLead->dropTableIfExists()->install();
      $mLeadHistory->dropTableIfExists()->install();
      $mLeadTag->dropTableIfExists()->install();
      $mCrossLeadTag->dropTableIfExists()->install();
      $mLeadActivity->dropTableIfExists()->install();
      $mLeadDocument->dropTableIfExists()->install();

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

  // public function installDefaultPermissions(): void
  // {
  //   $mPermission = $this->main->di->create(\HubletoApp\Community\Settings\Models\Permission::class);
  //   $permissions = [
  //     "HubletoApp/Community/Leads/Models/Lead:Create",
  //     "HubletoApp/Community/Leads/Models/Lead:Read",
  //     "HubletoApp/Community/Leads/Models/Lead:Update",
  //     "HubletoApp/Community/Leads/Models/Lead:Delete",

  //     "HubletoApp/Community/Leads/Models/LeadActivity:Create",
  //     "HubletoApp/Community/Leads/Models/LeadActivity:Read",
  //     "HubletoApp/Community/Leads/Models/LeadActivity:Update",
  //     "HubletoApp/Community/Leads/Models/LeadActivity:Delete",

  //     "HubletoApp/Community/Leads/Models/LeadDocument:Create",
  //     "HubletoApp/Community/Leads/Models/LeadDocument:Read",
  //     "HubletoApp/Community/Leads/Models/LeadDocument:Update",
  //     "HubletoApp/Community/Leads/Models/LeadDocument:Delete",

  //     "HubletoApp/Community/Leads/Models/LeadHistory:Create",
  //     "HubletoApp/Community/Leads/Models/LeadHistory:Read",
  //     "HubletoApp/Community/Leads/Models/LeadHistory:Update",
  //     "HubletoApp/Community/Leads/Models/LeadHistory:Delete",

  //     "HubletoApp/Community/Leads/Models/LeadProduct:Create",
  //     "HubletoApp/Community/Leads/Models/LeadProduct:Read",
  //     "HubletoApp/Community/Leads/Models/LeadProduct:Update",
  //     "HubletoApp/Community/Leads/Models/LeadProduct:Delete",

  //     "HubletoApp/Community/Leads/Models/LeadTag:Create",
  //     "HubletoApp/Community/Leads/Models/LeadTag:Read",
  //     "HubletoApp/Community/Leads/Models/LeadTag:Update",
  //     "HubletoApp/Community/Leads/Models/LeadTag:Delete",

  //     "HubletoApp/Community/Leads/Controllers/Leads",
  //     "HubletoApp/Community/Leads/Controllers/LeadsArchive",

  //     "HubletoApp/Community/Leads/Api/ConvertToDeal",
  //     "HubletoApp/Community/Leads/Api/GetCalendarEvents",

  //     "HubletoApp/Community/Leads/Leads"
  //   ];

  //   foreach ($permissions as $permission) {
  //     $mPermission->record->recordCreate([
  //       "permission" => $permission
  //     ]);
  //   }
  // }

}
