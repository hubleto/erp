<?php

namespace CeremonyCrmMod\Sales\Deals;

class Loader extends \CeremonyCrmApp\Core\Module
{


  public function __construct(\CeremonyCrmApp $app)
  {
    parent::__construct($app);
  }

  public function init(): void
  {
    $this->app->router->httpGet([
      '/^sales\/deals\/?$/' => Controllers\Deals::class,
      '/^sales\/change-pipeline\/?$/' => Controllers\ChangePipeline::class,
      '/^sales\/change-pipeline-step\/?$/' => Controllers\ChangePipelineStep::class,
      '/^sales\/deals\/archive\/?$/' => Controllers\DealsArchive::class,
    ]);

    if (str_starts_with($this->app->requestedUri, 'sales')) {
      $this->app->sidebar->addLink(2, 10203, 'sales/deals', $this->translate('Deals'), 'fa-regular fa-handshake');
      $this->app->sidebar->addLink(2, 10205, 'sales/deals/archive', $this->translate('Deals Archive'), 'fas fa-box-archive');
    }
  }

  public function installTables()
  {
    $mDeal = new \CeremonyCrmMod\Sales\Deals\Models\Deal($this->app);
    $mDealHistory = new \CeremonyCrmMod\Sales\Deals\Models\DealHistory($this->app);
    $mDealLabel = new \CeremonyCrmMod\Sales\Deals\Models\DealLabel($this->app);
    $mDealService = new \CeremonyCrmMod\Sales\Deals\Models\DealService($this->app);
    $mDealActivity = new \CeremonyCrmMod\Sales\Deals\Models\DealActivity($this->app);
    $mDealDocument = new \CeremonyCrmMod\Sales\Deals\Models\DealDocument($this->app);

    $mDeal->dropTableIfExists()->install();
    $mDealHistory->dropTableIfExists()->install();
    $mDealLabel->dropTableIfExists()->install();
    $mDealService->dropTableIfExists()->install();
    $mDealActivity->dropTableIfExists()->install();
    $mDealDocument->dropTableIfExists()->install();
  }
}