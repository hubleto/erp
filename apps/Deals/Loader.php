<?php

namespace HubletoApp\Community\Deals;

class Loader extends \Hubleto\Framework\App
{
  public bool $hasCustomSettings = true;

  public function init(): void
  {
    parent::init();

    $this->main->router->httpGet([
      '/^deals(\/(?<recordId>\d+))?\/?$/' => Controllers\Deals::class,
      '/^deals\/add\/?$/' => ['controller' => Controllers\Deals::class, 'vars' => ['recordId' => -1]],
      '/^deals\/settings\/?$/' => Controllers\Settings::class,
      '/^deals\/archive\/?$/' => Controllers\DealsArchive::class,
      '/^deals\/change-pipeline\/?$/' => Controllers\Api\ChangePipeline::class,
      '/^deals\/api\/log-activity\/?$/' => Controllers\Api\LogActivity::class,
      '/^deals\/api\/convert-lead-to-deal\/?$/' => Controllers\Api\ConvertLeadToDeal::class,
      '/^settings\/deal-tags\/?$/' => Controllers\Tags::class,
      '/^settings\/deal-lost-reasons\/?$/' => Controllers\LostReasons::class,
      '/^deals\/boards\/deal-warnings\/?$/' => Controllers\Boards\DealWarnings::class,
      '/^deals\/boards\/most-valuable-deals\/?$/' => Controllers\Boards\MostValuableDeals::class,
      '/^deals\/boards\/deal-value-by-result\/?$/' => Controllers\Boards\DealValueByResult::class,
    ]);

    $this->main->apps->community('Settings')?->addSetting($this, [
      'title' => $this->translate('Deal Tags'),
      'icon' => 'fas fa-tags',
      'url' => 'settings/deal-tags',
    ]);
    $this->main->apps->community('Settings')?->addSetting($this, [
      'title' => $this->translate('Deal Lost Reasons'),
      'icon' => 'fas fa-tags',
      'url' => 'settings/deal-lost-reasons',
    ]);

    $calendarManager = $this->main->apps->community('Calendar')?->calendarManager?->addCalendar(
      'deals',
      $this->configAsString('calendarColor'),
      Calendar::class,
    );

    $reportManager = $this->main->apps->community('Reports')?->reportManager?->addReport($this, Reports\MonthlyRevenue::class);

    $this->main->apps->community('Tasks')?->registerExternalModel($this, Models\Deal::class);

    $dashboardsApp = $this->main->apps->community('Dashboards');
    if ($dashboardsApp) {
      $dashboardsApp->addBoard(
        $this,
        $this->translate('Deal warnings'),
        'deals/boards/deal-warnings'
      );

      $dashboardsApp->addBoard(
        $this,
        $this->translate('Most valuable deals'),
        'deals/boards/most-valuable-deals'
      );

      $dashboardsApp->addBoard(
        $this,
        $this->translate('Deal value by result'),
        'deals/boards/deal-value-by-result'
      );
    }

    $this->main->apps->community('Help')?->addContextHelpUrls('/^deals\/?$/', [
      'en' => 'en/apps/community/deals',
    ]);

    $appMenu = $this->main->apps->community('Desktop')->appMenu;
    $appMenu->addItem($this, 'deals', $this->translate('Active deals'), 'fas fa-handshake');
    $appMenu->addItem($this, 'deals/archive', $this->translate('Archived deals'), 'fas fa-box-archive');
  }

  public function installTables(int $round): void
  {
    if ($round == 1) {
      $mDeal = $this->main->di->create(\HubletoApp\Community\Deals\Models\Deal::class);
      $mDealHistory = $this->main->di->create(\HubletoApp\Community\Deals\Models\DealHistory::class);
      $mDealTag = $this->main->di->create(\HubletoApp\Community\Deals\Models\Tag::class);
      $mCrossDealTag = $this->main->di->create(\HubletoApp\Community\Deals\Models\DealTag::class);
      $mDealProduct = $this->main->di->create(\HubletoApp\Community\Deals\Models\DealProduct::class);
      $mDealActivity = $this->main->di->create(\HubletoApp\Community\Deals\Models\DealActivity::class);
      $mDealDocument = $this->main->di->create(\HubletoApp\Community\Deals\Models\DealDocument::class);
      $mLostReasons = $this->main->di->create(\HubletoApp\Community\Deals\Models\LostReason::class);

      $mLostReasons->dropTableIfExists()->install();
      $mDeal->dropTableIfExists()->install();
      $mDealHistory->dropTableIfExists()->install();
      $mDealTag->dropTableIfExists()->install();
      $mCrossDealTag->dropTableIfExists()->install();
      $mDealProduct->dropTableIfExists()->install();
      $mDealActivity->dropTableIfExists()->install();
      $mDealDocument->dropTableIfExists()->install();

      $mDealTag->record->recordCreate([ 'name' => "Important", 'color' => '#fc2c03' ]);
      $mDealTag->record->recordCreate([ 'name' => "ASAP", 'color' => '#62fc03' ]);
      $mDealTag->record->recordCreate([ 'name' => "Extenstion", 'color' => '#033dfc' ]);
      $mDealTag->record->recordCreate([ 'name' => "New Customer", 'color' => '#fcdb03' ]);
      $mDealTag->record->recordCreate([ 'name' => "Existing Customer", 'color' => '#5203fc' ]);

      $mLostReasons->record->recordCreate(["reason" => "Price"]);
      $mLostReasons->record->recordCreate(["reason" => "Solution"]);
      $mLostReasons->record->recordCreate(["reason" => "Demand canceled by customer"]);
      $mLostReasons->record->recordCreate(["reason" => "Other"]);
    }
  }

  // public function installDefaultPermissions(): void
  // {
  //   $mPermission = $this->main->di->create(\HubletoApp\Community\Settings\Models\Permission::class);
  //   $permissions = [
  //     "HubletoApp/Community/Deals/Models/Deal:Create",
  //     "HubletoApp/Community/Deals/Models/Deal:Read",
  //     "HubletoApp/Community/Deals/Models/Deal:Update",
  //     "HubletoApp/Community/Deals/Models/Deal:Delete",

  //     "HubletoApp/Community/Deals/Models/DealActivity:Create",
  //     "HubletoApp/Community/Deals/Models/DealActivity:Read",
  //     "HubletoApp/Community/Deals/Models/DealActivity:Update",
  //     "HubletoApp/Community/Deals/Models/DealActivity:Delete",

  //     "HubletoApp/Community/Deals/Models/DealDocument:Create",
  //     "HubletoApp/Community/Deals/Models/DealDocument:Read",
  //     "HubletoApp/Community/Deals/Models/DealDocument:Update",
  //     "HubletoApp/Community/Deals/Models/DealDocument:Delete",

  //     "HubletoApp/Community/Deals/Models/DealHistory:Create",
  //     "HubletoApp/Community/Deals/Models/DealHistory:Read",
  //     "HubletoApp/Community/Deals/Models/DealHistory:Update",
  //     "HubletoApp/Community/Deals/Models/DealHistory:Delete",

  //     "HubletoApp/Community/Deals/Models/DealProduct:Create",
  //     "HubletoApp/Community/Deals/Models/DealProduct:Read",
  //     "HubletoApp/Community/Deals/Models/DealProduct:Update",
  //     "HubletoApp/Community/Deals/Models/DealProduct:Delete",

  //     "HubletoApp/Community/Deals/Models/DealTag:Create",
  //     "HubletoApp/Community/Deals/Models/DealTag:Read",
  //     "HubletoApp/Community/Deals/Models/DealTag:Update",
  //     "HubletoApp/Community/Deals/Models/DealTag:Delete",

  //     "HubletoApp/Community/Deals/Controllers/Deals",
  //     "HubletoApp/Community/Deals/Controllers/DealsArchive",

  //     "HubletoApp/Community/Deals/Api/GetCalendarEvents",

  //     "HubletoApp/Community/Deals/Deals",
  //   ];

  //   foreach ($permissions as $permission) {
  //     $mPermission->record->recordCreate([
  //       "permission" => $permission
  //     ]);
  //   }
  // }
}
