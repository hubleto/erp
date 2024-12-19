<?php

namespace CeremonyCrmMod\Leads;

class Loader extends \CeremonyCrmApp\Core\Module
{


  public function __construct(\CeremonyCrmApp $app)
  {
    parent::__construct($app);
  }

  public function init(): void
  {
    $this->app->router->httpGet([
      '/^leads\/?$/' => Controllers\Leads::class,
      '/^leads\/archive\/?$/' => Controllers\LeadsArchive::class,
      '/^leads\/get-calendar-events\/?$/' => Controllers\Api\GetCalendarEvents::class,
      '/^leads\/convert-to-deal\/?$/' => Controllers\Api\ConvertLead::class,
      '/^settings\/lead-statuses\/?$/' => Controllers\LeadStatuses::class,
    ]);

    $this->app->sidebar->addLink(1, 100, 'leads', $this->translate('Leads'), 'fas fa-people-arrows', str_starts_with($this->app->requestedUri, 'leads'));

    $this->app->addSetting([
      'title' => $this->translate('Lead statuses'),
      'icon' => 'fas fa-arrow-up-short-wide',
      'url' => 'settings/lead-statuses',
    ]);

    $this->app->addCalendar(Calendar::class);
  }

  public function installTables()
  {
    $mLeadStatus = new Models\LeadStatus($this->app);
    $mLead = new \CeremonyCrmMod\Leads\Models\Lead($this->app);
    $mLeadHistory = new \CeremonyCrmMod\Leads\Models\LeadHistory($this->app);
    $mLeadTag = new \CeremonyCrmMod\Leads\Models\LeadTag($this->app);
    $mLeadService = new \CeremonyCrmMod\Leads\Models\LeadService($this->app);
    $mLeadActivity = new \CeremonyCrmMod\Leads\Models\LeadActivity($this->app);
    $mLeadDocument = new \CeremonyCrmMod\Leads\Models\LeadDocument($this->app);

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