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
    ]);

    $this->app->sidebar->addLink(1, 100, 'leads', $this->translate('Leads'), 'fas fa-people-arrows', str_starts_with($this->app->requestedUri, 'leads'));

    $this->app->addCalendar(Calendar::class);

    // if (str_starts_with($this->app->requestedUri, 'sales')) {
    //   $this->app->sidebar->addLink(2, 10202, 'leads', $this->translate('Leads'), 'fas fa-arrows-turn-to-dots');
    //   $this->app->sidebar->addLink(2, 10204, 'leads/archive', $this->translate('Leads Archive'), 'fas fa-box-archive');
    // }
  }

  public function installTables()
  {
    $mLead = new \CeremonyCrmMod\Leads\Models\Lead($this->app);
    $mLeadHistory = new \CeremonyCrmMod\Leads\Models\LeadHistory($this->app);
    $mLeadTag = new \CeremonyCrmMod\Leads\Models\LeadTag($this->app);
    $mLeadService = new \CeremonyCrmMod\Leads\Models\LeadService($this->app);
    $mLeadActivity = new \CeremonyCrmMod\Leads\Models\LeadActivity($this->app);
    $mLeadDocument = new \CeremonyCrmMod\Leads\Models\LeadDocument($this->app);

    $mLead->dropTableIfExists()->install();
    $mLeadHistory->dropTableIfExists()->install();
    $mLeadTag->dropTableIfExists()->install();
    $mLeadService->dropTableIfExists()->install();
    $mLeadActivity->dropTableIfExists()->install();
    $mLeadDocument->dropTableIfExists()->install();
  }

}