<?php

namespace HubletoApp\Community\Deals;

class Loader extends \HubletoMain\Core\App
{

  public function init(): void
  {
    parent::init();

    $this->main->router->httpGet([
      '/^deals\/?$/' => Controllers\Deals::class,
      '/^deals\/get-calendar-events\/?$/' => Controllers\Api\GetCalendarEvents::class,
      '/^deals\/archive\/?$/' => Controllers\DealsArchive::class,
      '/^deals\/change-pipeline\/?$/' => Controllers\Api\ChangePipeline::class,
      '/^deals\/change-pipeline-step\/?$/' => Controllers\Api\ChangePipelineStep::class,
      '/^settings\/deal-statuses\/?$/' => Controllers\DealStatuses::class,
    ]);

    $this->main->addSetting([
      'title' => $this->translate('Deal statuses'),
      'icon' => 'fas fa-arrow-up-short-wide',
      'url' => 'settings/deal-statuses',
    ]);

    $this->main->calendarManager->addCalendar(Calendar::class);
    $this->main->reportManager->addReport(Reports\MonthlyRevenue::class);

    $this->main->help->addContextHelpUrls('/^deals\/?$/', [
      'en' => 'en/apps/community/deals',
    ]);
  }

  public function installTables(): void
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

    $mDealStatus->eloquent->create([ 'name' => 'New', 'order' => 1, 'color' => '#0000A0' ]);
    $mDealStatus->eloquent->create([ 'name' => 'In Progress', 'order' => 2, 'color' => '#A0A000' ]);
    $mDealStatus->eloquent->create([ 'name' => 'Won', 'order' => 3, 'color' => '#00A000' ]);
    $mDealStatus->eloquent->create([ 'name' => 'Lost', 'order' => 4, 'color' => '#A00000' ]);

  }

  public function installDefaultPermissions(): void
  {
    $mPermission = new \HubletoApp\Community\Settings\Models\Permission($this->main);
    $permissions = [
      "HubletoApp/Community/Deals/Models/Deal:Create",
      "HubletoApp/Community/Deals/Models/Deal:Read",
      "HubletoApp/Community/Deals/Models/Deal:Update",
      "HubletoApp/Community/Deals/Models/Deal:Delete",

      "HubletoApp/Community/Deals/Models/DealActivity:Create",
      "HubletoApp/Community/Deals/Models/DealActivity:Read",
      "HubletoApp/Community/Deals/Models/DealActivity:Update",
      "HubletoApp/Community/Deals/Models/DealActivity:Delete",

      "HubletoApp/Community/Deals/Models/DealDocument:Create",
      "HubletoApp/Community/Deals/Models/DealDocument:Read",
      "HubletoApp/Community/Deals/Models/DealDocument:Update",
      "HubletoApp/Community/Deals/Models/DealDocument:Delete",

      "HubletoApp/Community/Deals/Models/DealHistory:Create",
      "HubletoApp/Community/Deals/Models/DealHistory:Read",
      "HubletoApp/Community/Deals/Models/DealHistory:Update",
      "HubletoApp/Community/Deals/Models/DealHistory:Delete",

      "HubletoApp/Community/Deals/Models/DealService:Create",
      "HubletoApp/Community/Deals/Models/DealService:Read",
      "HubletoApp/Community/Deals/Models/DealService:Update",
      "HubletoApp/Community/Deals/Models/DealService:Delete",

      "HubletoApp/Community/Deals/Models/DealStatus:Create",
      "HubletoApp/Community/Deals/Models/DealStatus:Read",
      "HubletoApp/Community/Deals/Models/DealStatus:Update",
      "HubletoApp/Community/Deals/Models/DealStatus:Delete",

      "HubletoApp/Community/Deals/Models/DealTag:Create",
      "HubletoApp/Community/Deals/Models/DealTag:Read",
      "HubletoApp/Community/Deals/Models/DealTag:Update",
      "HubletoApp/Community/Deals/Models/DealTag:Delete",

      "HubletoApp/Community/Deals/Controllers/Deals",
      "HubletoApp/Community/Deals/Controllers/DealsArchive",
      "HubletoApp/Community/Deals/Controllers/DealStatuses",

      "HubletoApp/Community/Deals/Api/ChangePipelineStep",
      "HubletoApp/Community/Deals/Api/GetCalendarEvents",

      "HubletoApp/Community/Deals/Deals",
    ];

    foreach ($permissions as $permission) {
      $mPermission->eloquent->create([
        "permission" => $permission
      ]);
    }
  }
}