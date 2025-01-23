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

  public function installDefaultPermissions()
  {
    $mPermission = new \HubletoApp\Community\Settings\Models\Permission($this->main);
    $permissions = [
      "HubletoApp/Community/Deals/Models/Deal:Create" => "Deal/Create",
      "HubletoApp/Community/Deals/Models/Deal:Read" => "Deal/Read",
      "HubletoApp/Community/Deals/Models/Deal:Update" => "Deal/Update",
      "HubletoApp/Community/Deals/Models/Deal:Delete" => "Deal/Delete",
      "HubletoApp/Community/Deals/Models/DealActivity:Create" => "DealActivity/Create",
      "HubletoApp/Community/Deals/Models/DealActivity:Read" => "DealActivity/Read",
      "HubletoApp/Community/Deals/Models/DealActivity:Update" => "DealActivity/Update",
      "HubletoApp/Community/Deals/Models/DealActivity:Delete" => "DealActivity/Delete",
      "HubletoApp/Community/Deals/Models/DealDocument:Create" => "DealDocument/Create",
      "HubletoApp/Community/Deals/Models/DealDocument:Read" => "DealDocument/Read",
      "HubletoApp/Community/Deals/Models/DealDocument:Update" => "DealDocument/Update",
      "HubletoApp/Community/Deals/Models/DealDocument:Delete" => "DealDocument/Delete",
      "HubletoApp/Community/Deals/Models/DealHistory:Create" => "DealHistory/Create",
      "HubletoApp/Community/Deals/Models/DealHistory:Read" => "DealHistory/Read",
      "HubletoApp/Community/Deals/Models/DealHistory:Update" => "DealHistory/Update",
      "HubletoApp/Community/Deals/Models/DealHistory:Delete" => "DealHistory/Delete",
      "HubletoApp/Community/Deals/Models/DealService:Create" => "DealService/Create",
      "HubletoApp/Community/Deals/Models/DealService:Read" => "DealService/Read",
      "HubletoApp/Community/Deals/Models/DealService:Update" => "DealService/Update",
      "HubletoApp/Community/Deals/Models/DealService:Delete" => "DealService/Delete",
      "HubletoApp/Community/Deals/Models/DealStatus:Create" => "DealStatus/Create",
      "HubletoApp/Community/Deals/Models/DealStatus:Read" => "DealStatus/Read",
      "HubletoApp/Community/Deals/Models/DealStatus:Update" => "DealStatus/Update",
      "HubletoApp/Community/Deals/Models/DealStatus:Delete" => "DealStatus/Delete",
      "HubletoApp/Community/Deals/Models/DealTag:Create" => "DealTag/Create",
      "HubletoApp/Community/Deals/Models/DealTag:Read" => "DealTag/Read",
      "HubletoApp/Community/Deals/Models/DealTag:Update" => "DealTag/Update",
      "HubletoApp/Community/Deals/Models/DealTag:Delete" => "DealTag/Delete",

      "HubletoApp/Community/Deals/Controllers/Deals" => "Deals/Controller",
      "HubletoApp/Community/Deals/Controllers/DealsArchive" => "DealsArchive/Controller",
      "HubletoApp/Community/Deals/Controllers/DealStatuses" => "DealStatuses/Controller",

      "HubletoApp/Community/Deals/Api/ChangePipelineStep" => "Company/Api/ChangePipelineStep",
      "HubletoApp/Community/Deals/Api/GetCalendarEvents" => "Company/Api/GetCalendarEvents",

      "HubletoApp/Community/Deals/Deals" => "Deal",
      "HubletoApp/Community/Deals/DealActivities" => "DealActivity",
      "HubletoApp/Community/Deals/DealDocuments" => "DealDocument",
      "HubletoApp/Community/Deals/DealHistories" => "DealHistory",
      "HubletoApp/Community/Deals/DealServices" => "DealService",
      "HubletoApp/Community/Deals/DealStatuses" => "DealStatus",
      "HubletoApp/Community/Deals/DealTags" => "DealTag",
    ];

    foreach ($permissions as $permission => $allias) {
      $mPermission->eloquent->create([
        "permission" => $permission,
        "allias" => $allias,
      ]);
    }
  }
}