<?php

namespace CeremonyCrmMod\Deals;

class Loader extends \CeremonyCrmApp\Core\Module
{


  public function __construct(\CeremonyCrmApp $app)
  {
    parent::__construct($app);
  }

  public function init(): void
  {
    $this->app->router->httpGet([
      '/^deals\/?$/' => Controllers\Deals::class,
      '/^deals\/get-calendar-events\/?$/' => Controllers\Api\GetCalendarEvents::class,
      '/^deals\/archive\/?$/' => Controllers\DealsArchive::class,
      '/^deals\/change-pipeline\/?$/' => Controllers\Api\ChangePipeline::class,
      '/^deals\/change-pipeline-step\/?$/' => Controllers\Api\ChangePipelineStep::class,
    ]);

    $this->app->sidebar->addLink(1, 200, 'deals', $this->translate('Deals'), 'fas fa-handshake', str_starts_with($this->app->requestedUri, 'deals'));

    $this->app->addCalendar(Calendar::class);

    // if (str_starts_with($this->app->requestedUri, 'sales')) {
    //   $this->app->sidebar->addLink(2, 10203, 'deals', $this->translate('Deals'), 'fa-regular fa-handshake');
    //   $this->app->sidebar->addLink(2, 10205, 'deals/archive', $this->translate('Deals Archive'), 'fas fa-box-archive');
    // }
  }

  public function installTables()
  {
    $mDeal = new \CeremonyCrmMod\Deals\Models\Deal($this->app);
    $mDealHistory = new \CeremonyCrmMod\Deals\Models\DealHistory($this->app);
    $mDealLabel = new \CeremonyCrmMod\Deals\Models\DealLabel($this->app);
    $mDealService = new \CeremonyCrmMod\Deals\Models\DealService($this->app);
    $mDealActivity = new \CeremonyCrmMod\Deals\Models\DealActivity($this->app);
    $mDealDocument = new \CeremonyCrmMod\Deals\Models\DealDocument($this->app);

    $mDeal->dropTableIfExists()->install();
    $mDealHistory->dropTableIfExists()->install();
    $mDealLabel->dropTableIfExists()->install();
    $mDealService->dropTableIfExists()->install();
    $mDealActivity->dropTableIfExists()->install();
    $mDealDocument->dropTableIfExists()->install();
  }
}