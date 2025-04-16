<?php

namespace HubletoApp\Community\Leads;

class Loader extends \HubletoMain\Core\App
{

  public bool $hasCustomSettings = true;

  public function init(): void
  {
    parent::init();

    $this->main->router->httpGet([
      '/^leads(\/(?<recordId>\d+))?\/?$/' => Controllers\Leads::class,
      '/^leads\/settings\/?$/' => Controllers\Settings::class,
      '/^leads\/archive\/?$/' => Controllers\LeadsArchive::class,
      '/^leads\/get-calendar-events\/?$/' => Controllers\Api\GetCalendarEvents::class,
      '/^leads\/convert-to-deal\/?$/' => Controllers\Api\ConvertLead::class,
      '/^settings\/lead-statuses\/?$/' => Controllers\LeadStatuses::class,
      '/^settings\/lead-tags\/?$/' => Controllers\Tags::class,
    ]);

    $this->main->addSetting($this, [
      'title' => $this->translate('Lead statuses'),
      'icon' => 'fas fa-arrow-up-short-wide',
      'url' => 'settings/lead-statuses',
    ]);
    $this->main->addSetting($this, [
      'title' => $this->translate('Lead Tags'),
      'icon' => 'fas fa-tags',
      'url' => 'settings/lead-tags',
    ]);

    $calendarManager = $this->main->apps->community('Calendar')->calendarManager;
    $calendarManager->addCalendar(Calendar::class);

    $this->main->apps->community('Help')->addContextHelpUrls('/^leads\/?$/', [
      'en' => 'en/apps/community/leads',
    ]);
  }

  public function installTables(int $round): void
  {
    if ($round == 1) {
      $mLeadStatus = new Models\LeadStatus($this->main);
      $mLead = new \HubletoApp\Community\Leads\Models\Lead($this->main);
      $mLeadHistory = new \HubletoApp\Community\Leads\Models\LeadHistory($this->main);
      $mLeadTag = new \HubletoApp\Community\Leads\Models\Tag($this->main);
      $mCrossLeadTag = new \HubletoApp\Community\Leads\Models\LeadTag($this->main);
      $mLeadProduct = new \HubletoApp\Community\Leads\Models\LeadProduct($this->main);
      $mLeadActivity = new \HubletoApp\Community\Leads\Models\LeadActivity($this->main);
      $mLeadDocument = new \HubletoApp\Community\Leads\Models\LeadDocument($this->main);

      $mLeadStatus->dropTableIfExists()->install();
      $mLead->dropTableIfExists()->install();
      $mLeadHistory->dropTableIfExists()->install();
      $mLeadTag->dropTableIfExists()->install();
      $mCrossLeadTag->dropTableIfExists()->install();
      $mLeadProduct->dropTableIfExists()->install();
      $mLeadActivity->dropTableIfExists()->install();
      $mLeadDocument->dropTableIfExists()->install();

      $mLeadTag->record->recordCreate([ 'name' => "Quite complex", 'color' => '#2196f3' ]);
      $mLeadTag->record->recordCreate([ 'name' => "Great opportunity", 'color' => '#4caf50' ]);
      $mLeadTag->record->recordCreate([ 'name' => "Duplicate", 'color' => '#9e9e9e' ]);
      $mLeadTag->record->recordCreate([ 'name' => "Needs attention", 'color' => '#795548' ]);

      $mLeadStatus->record->recordCreate([ 'name' => 'New', 'order' => 1, 'color' => '#f55442' ]);
      $mLeadStatus->record->recordCreate([ 'name' => 'In Progress', 'order' => 2, 'color' => '#f5bc42' ]);
      $mLeadStatus->record->recordCreate([ 'name' => 'Completed', 'order' => 3, 'color' => '#42ddf5' ]);
      $mLeadStatus->record->recordCreate([ 'name' => 'Lost', 'order' => 4, 'color' => '#f55442' ]);
    }
  }

  public function installDefaultPermissions(): void
  {
    $mPermission = new \HubletoApp\Community\Settings\Models\Permission($this->main);
    $permissions = [
      "HubletoApp/Community/Leads/Models/Lead:Create",
      "HubletoApp/Community/Leads/Models/Lead:Read",
      "HubletoApp/Community/Leads/Models/Lead:Update",
      "HubletoApp/Community/Leads/Models/Lead:Delete",

      "HubletoApp/Community/Leads/Models/LeadActivity:Create",
      "HubletoApp/Community/Leads/Models/LeadActivity:Read",
      "HubletoApp/Community/Leads/Models/LeadActivity:Update",
      "HubletoApp/Community/Leads/Models/LeadActivity:Delete",

      "HubletoApp/Community/Leads/Models/LeadDocument:Create",
      "HubletoApp/Community/Leads/Models/LeadDocument:Read",
      "HubletoApp/Community/Leads/Models/LeadDocument:Update",
      "HubletoApp/Community/Leads/Models/LeadDocument:Delete",

      "HubletoApp/Community/Leads/Models/LeadHistory:Create",
      "HubletoApp/Community/Leads/Models/LeadHistory:Read",
      "HubletoApp/Community/Leads/Models/LeadHistory:Update",
      "HubletoApp/Community/Leads/Models/LeadHistory:Delete",

      "HubletoApp/Community/Leads/Models/LeadProduct:Create",
      "HubletoApp/Community/Leads/Models/LeadProduct:Read",
      "HubletoApp/Community/Leads/Models/LeadProduct:Update",
      "HubletoApp/Community/Leads/Models/LeadProduct:Delete",

      "HubletoApp/Community/Leads/Models/LeadStatus:Create",
      "HubletoApp/Community/Leads/Models/LeadStatus:Read",
      "HubletoApp/Community/Leads/Models/LeadStatus:Update",
      "HubletoApp/Community/Leads/Models/LeadStatus:Delete",

      "HubletoApp/Community/Leads/Models/LeadTag:Create",
      "HubletoApp/Community/Leads/Models/LeadTag:Read",
      "HubletoApp/Community/Leads/Models/LeadTag:Update",
      "HubletoApp/Community/Leads/Models/LeadTag:Delete",

      "HubletoApp/Community/Leads/Controllers/Leads",
      "HubletoApp/Community/Leads/Controllers/LeadsArchive",
      "HubletoApp/Community/Leads/Controllers/LeadStatuses",

      "HubletoApp/Community/Leads/Api/ConvertLead",
      "HubletoApp/Community/Leads/Api/GetCalendarEvents",

      "HubletoApp/Community/Leads/Leads"
    ];

    foreach ($permissions as $permission) {
      $mPermission->record->recordCreate([
        "permission" => $permission
      ]);
    }
  }

}