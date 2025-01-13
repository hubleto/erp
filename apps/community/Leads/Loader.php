<?php

namespace HubletoApp\Community\Leads;

class Loader extends \HubletoMain\Core\App
{


  public function __construct(\HubletoMain $main)
  {
    parent::__construct($main);
  }

  public function init(): void
  {
    $this->main->router->httpGet([
      '/^leads\/?$/' => Controllers\Leads::class,
      '/^leads\/archive\/?$/' => Controllers\LeadsArchive::class,
      '/^leads\/get-calendar-events\/?$/' => Controllers\Api\GetCalendarEvents::class,
      '/^leads\/convert-to-deal\/?$/' => Controllers\Api\ConvertLead::class,
      '/^settings\/lead-statuses\/?$/' => Controllers\LeadStatuses::class,
    ]);

    $this->main->sidebar->addLink(1, 100, 'leads', $this->translate('Leads'), 'fas fa-people-arrows', str_starts_with($this->main->requestedUri, 'leads'));

    $this->main->addSetting([
      'title' => $this->translate('Lead statuses'),
      'icon' => 'fas fa-arrow-up-short-wide',
      'url' => 'settings/lead-statuses',
    ]);

    $this->main->addCalendar(Calendar::class);
  }

  public function installTables()
  {
    $mLeadStatus = new Models\LeadStatus($this->main);
    $mLead = new \HubletoApp\Community\Leads\Models\Lead($this->main);
    $mLeadHistory = new \HubletoApp\Community\Leads\Models\LeadHistory($this->main);
    $mLeadTag = new \HubletoApp\Community\Leads\Models\LeadTag($this->main);
    $mLeadService = new \HubletoApp\Community\Leads\Models\LeadService($this->main);
    $mLeadActivity = new \HubletoApp\Community\Leads\Models\LeadActivity($this->main);
    $mLeadDocument = new \HubletoApp\Community\Leads\Models\LeadDocument($this->main);

    $mLeadStatus->dropTableIfExists()->install();
    $mLead->dropTableIfExists()->install();
    $mLeadHistory->dropTableIfExists()->install();
    $mLeadTag->dropTableIfExists()->install();
    $mLeadService->dropTableIfExists()->install();
    $mLeadActivity->dropTableIfExists()->install();
    $mLeadDocument->dropTableIfExists()->install();

    $mLeadStatus->eloquent->create([ 'name' => 'New', 'order' => 1, 'color' => '#f55442' ]);
    $mLeadStatus->eloquent->create([ 'name' => 'In Progress', 'order' => 2, 'color' => '#f5bc42' ]);
    $mLeadStatus->eloquent->create([ 'name' => 'Closed', 'order' => 3, 'color' => '#42ddf5' ]);
    $mLeadStatus->eloquent->create([ 'name' => 'Lost', 'order' => 4, 'color' => '#f55442' ]);

  }

}