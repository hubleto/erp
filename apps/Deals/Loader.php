<?php

namespace Hubleto\App\Community\Deals;

class Loader extends \Hubleto\Framework\App
{
  // public bool $hasCustomSettings = true;

  /**
   * Inits the app: adds routes, settings, calendars, hooks, menu items, ...
   *
   * @return void
   * 
   */
  public function init(): void
  {
    parent::init();

    $this->router()->get([
      '/^deals\/api\/log-activity\/?$/' => Controllers\Api\LogActivity::class,
      '/^deals\/api\/create-from-lead\/?$/' => Controllers\Api\CreateFromLead::class,
      '/^deals\/api\/generate-quotation-pdf\/?$/' => Controllers\Api\GenerateQuotationPdf::class,
      '/^deals\/api\/generate-invoice\/?$/' => Controllers\Api\GenerateInvoice::class,

      '/^deals\/boards\/deal-warnings\/?$/' => Controllers\Boards\DealWarnings::class,
      '/^deals\/boards\/most-valuable-deals\/?$/' => Controllers\Boards\MostValuableDeals::class,
      '/^deals\/boards\/deal-value-by-result\/?$/' => Controllers\Boards\DealValueByResult::class,

      '/^deals(\/(?<recordId>\d+))?\/?$/' => Controllers\Deals::class,
      '/^deals\/add\/?$/' => ['controller' => Controllers\Deals::class, 'vars' => ['recordId' => -1]],
      '/^deals\/tags\/?$/' => Controllers\Tags::class,
      '/^deals\/lost-reasons\/?$/' => Controllers\LostReasons::class,
      // '/^deals\/settings\/?$/' => Controllers\Settings::class,
    ]);
    
    $this->addSearchSwitch('d', 'deals');

    /** @var \Hubleto\App\Community\Settings\Loader $settingsApp */
    $settingsApp = $this->appManager()->getApp(\Hubleto\App\Community\Settings\Loader::class);
    $settingsApp->addSetting($this, [
      'title' => $this->translate('Deal Tags'),
      'icon' => 'fas fa-tags',
      'url' => 'deals/tags',
    ]);
    $settingsApp->addSetting($this, [
      'title' => $this->translate('Deal Lost Reasons'),
      'icon' => 'fas fa-tags',
      'url' => 'deals/lost-reasons',
    ]);

    /** @var \Hubleto\App\Community\Calendar\Manager */
    $calendarManager = $this->getService(\Hubleto\App\Community\Calendar\Manager::class);
    $calendarManager->addCalendar($this, 'deals', $this->configAsString('calendarColor'), Calendar::class);

    /** @var \Hubleto\App\Community\Workflow\Manager */
    $workflowManager = $this->getService(\Hubleto\App\Community\Workflow\Manager::class);
    $workflowManager->addWorkflow($this, 'deals', Workflow::class);

    // /** @var \Hubleto\App\Community\Reports\Loader */
    // $reportsApp = $this->appManager()->getApp(\Hubleto\App\Community\Reports\Loader::class);
    // if ($reportsApp != null) {
    //   $reportsApp->reportManager->addReport($this, Reports\MonthlyRevenue::class);
    // }

    /** @var \Hubleto\App\Community\Dashboards\Manager */
    $dashboardManager = $this->getService(\Hubleto\App\Community\Dashboards\Manager::class);
    $dashboardManager->addBoard($this, $this->translate('Deal warnings'), 'deals/boards/deal-warnings');
    $dashboardManager->addBoard($this, $this->translate('Most valuable deals'), 'deals/boards/most-valuable-deals');
    $dashboardManager->addBoard($this, $this->translate('Deal value by result'), 'deals/boards/deal-value-by-result');
  }

  public function installTables(int $round): void
  {
    if ($round == 1) {
      $mDeal = $this->getModel(Models\Deal::class);
      $mDealHistory = $this->getModel(Models\DealHistory::class);
      $mDealTag = $this->getModel(Models\Tag::class);
      $mCrossDealTag = $this->getModel(Models\DealTag::class);
      $mDealLead = $this->getModel(Models\DealLead::class);
      $mDealTask = $this->getModel(Models\DealTask::class);
      $mDealProduct = $this->getModel(Models\DealProduct::class);
      $mDealActivity = $this->getModel(Models\DealActivity::class);
      $mDealDocument = $this->getModel(Models\DealDocument::class);
      $mLostReasons = $this->getModel(Models\LostReason::class);

      $mLostReasons->dropTableIfExists()->install();
      $mDeal->dropTableIfExists()->install();
      $mDealHistory->dropTableIfExists()->install();
      $mDealTag->dropTableIfExists()->install();
      $mDealLead->dropTableIfExists()->install();
      $mDealTask->dropTableIfExists()->install();
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

  /**
   * Implements fulltext search functionality for tasks
   *
   * @param array $expressions List of expressions to be searched and glued with logical 'or'.
   * 
   * @return array
   * 
   */
  public function search(array $expressions): array
  {
    $mDeal = $this->getModel(Models\Deal::class);
    $qDeals = $mDeal->record->prepareReadQuery();
    
    foreach ($expressions as $e) {
      $qDeals = $qDeals->where(function($q) use ($e) {
        $q->orWhere('deals.identifier', 'like', '%' . $e . '%');
        $q->orWhere('deals.title', 'like', '%' . $e . '%');
      })
      ->where('deals.is_closed', false);
    }

    $deals = $qDeals->get()->toArray();

    $results = [];

    foreach ($deals as $deal) {
      $results[] = [
        "id" => $deal['id'],
        "label" => $deal['identifier'] . ' ' . $deal['title'],
        "url" => 'deals/' . $deal['id'],
        // "description" => $task[''],
      ];
    }

    return $results;
  }

}
