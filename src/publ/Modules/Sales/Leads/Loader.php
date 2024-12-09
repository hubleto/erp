<?php

namespace CeremonyCrmApp\Modules\Sales\Leads;

class Loader extends \CeremonyCrmApp\Core\Module
{

  public function __construct(\CeremonyCrmApp $app)
  {
    parent::__construct($app);
  }

  public function init(): void
  {
    $this->app->router->httpGet([
      '/^sales\/leads\/?$/' => Controllers\Leads::class,
      '/^sales\/convert-lead\/?$/' => Controllers\ConvertLead::class,
      '/^sales\/leads\/archive\/?$/' => Controllers\LeadsArchive::class,
    ]);

    // $router(
    //   'sales',
    //   'CeremonyCrmApp/Modules/Sales/Sales/Controllers',
    //   '@app/Modules/Sales/Sales/Views',
    //   [
    //     'idAccount' => '$1',
    //   ],
    //   [
    //     '' => 'Home',
    //     '/leads' => 'Leads',
    //     '/deals' => 'Deals',
    //     '/convert-lead' => 'ConvertLead',
    //     '/convert-lead' => 'ConvertLead',
    //     '/change-pipeline' => 'ChangePipeline',
    //     '/change-pipeline-step' => 'ChangePipelineStep',
    //   ]
    // );
  }

  public function modifySidebar(\CeremonyCrmApp\Core\Sidebar $sidebar)
  {
    if (str_starts_with($this->app->requestedUri, 'sales')) {
      $sidebar->addLink(2, 10202, 'sales/leads', $this->app->translate('Leads'), 'fas fa-arrows-turn-to-dots');
      $sidebar->addLink(2, 10204, 'sales/leads/archive', $this->app->translate('Leads Archive'), 'fas fa-box-archive');
    }
  }

  public function installTables()
  {
    $mLead = new \CeremonyCrmApp\Modules\Sales\Leads\Models\Lead($this->app);
    $mLeadHistory = new \CeremonyCrmApp\Modules\Sales\Leads\Models\LeadHistory($this->app);
    $mLeadLabel = new \CeremonyCrmApp\Modules\Sales\Leads\Models\LeadLabel($this->app);
    $mLeadService = new \CeremonyCrmApp\Modules\Sales\Leads\Models\LeadService($this->app);
    $mLeadActivity = new \CeremonyCrmApp\Modules\Sales\Leads\Models\LeadActivity($this->app);
    $mLeadDocument = new \CeremonyCrmApp\Modules\Sales\Leads\Models\LeadDocument($this->app);

    $mLead->dropTableIfExists()->install();
    $mLeadHistory->dropTableIfExists()->install();
    $mLeadLabel->dropTableIfExists()->install();
    $mLeadService->dropTableIfExists()->install();
    $mLeadActivity->dropTableIfExists()->install();
    $mLeadDocument->dropTableIfExists()->install();
  }

}