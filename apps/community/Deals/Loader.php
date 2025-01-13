<?php

namespace HubletoApp\Community\Deals;

class Loader extends \HubletoMain\Core\App
{


  public function __construct(\HubletoMain $main)
  {
    parent::__construct($main);
  }

  public function init(): void
  {
    $this->main->router->httpGet([
      '/^deals\/?$/' => Controllers\Deals::class,
      '/^deals\/get-calendar-events\/?$/' => Controllers\Api\GetCalendarEvents::class,
      '/^deals\/archive\/?$/' => Controllers\DealsArchive::class,
      '/^deals\/change-pipeline\/?$/' => Controllers\Api\ChangePipeline::class,
      '/^deals\/change-pipeline-step\/?$/' => Controllers\Api\ChangePipelineStep::class,
      '/^settings\/deal-statuses\/?$/' => Controllers\DealStatuses::class,
    ]);

    $this->main->sidebar->addLink(1, 200, 'deals', $this->translate('Deals'), 'fas fa-handshake', str_starts_with($this->main->requestedUri, 'deals'));

    $this->main->addSetting([
      'title' => $this->translate('Deal statuses'),
      'icon' => 'fas fa-arrow-up-short-wide',
      'url' => 'settings/deal-statuses',
    ]);

    $this->main->addCalendar(Calendar::class);
  }

  public function installTables()
  {
    $mDealStatus = new Models\DealStatus($this->main);
    $mDeal = new \HubletoApp\Community\Deals\Models\Deal($this->main);
    $mDealHistory = new \HubletoApp\Community\Deals\Models\DealHistory($this->main);
    $mDealTag = new \HubletoApp\Community\Deals\Models\DealTag($this->main);
    $mDealService = new \HubletoApp\Community\Deals\Models\DealService($this->main);
    $mDealActivity = new \HubletoApp\Community\Deals\Models\DealActivity($this->main);
    $mDealDocument = new \HubletoApp\Community\Deals\Models\DealDocument($this->main);

    $mDealStatus->dropTableIfExists()->install();
    $mDeal->dropTableIfExists()->install();
    $mDealHistory->dropTableIfExists()->install();
    $mDealTag->dropTableIfExists()->install();
    $mDealService->dropTableIfExists()->install();
    $mDealActivity->dropTableIfExists()->install();
    $mDealDocument->dropTableIfExists()->install();

    $mDealStatus->eloquent->create([ 'name' => 'New', 'order' => 1, 'color' => '#f55442' ]);
    $mDealStatus->eloquent->create([ 'name' => 'In Progress', 'order' => 2, 'color' => '#f5bc42' ]);
    $mDealStatus->eloquent->create([ 'name' => 'Closed', 'order' => 3, 'color' => '#42ddf5' ]);
    $mDealStatus->eloquent->create([ 'name' => 'Lost', 'order' => 4, 'color' => '#f55442' ]);

  }
}