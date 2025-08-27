<?php

namespace HubletoApp\Community\Reports;

use Hubleto\Framework\DependencyInjection;

class Loader extends \Hubleto\Framework\App
{
  public ReportManager $reportManager;

  public function __construct()
  {
    parent::__construct();
    // $this->reportManager = DependencyInjection::create($main, ReportManager::class);
    $this->reportManager = DependencyInjection::create(ReportManager::class);
  }

  /**
   * Inits the app: adds routes, settings, calendars, hooks, menu items, ...
   *
   * @return void
   * 
   */
  public function init(): void
  {
    parent::init();

    $this->getRouter()->httpGet([
      '/^reports\/?$/' => Controllers\Reports::class,
      '/^reports\/(?<reportUrlSlug>.*?)\/?$/' => Controllers\Report::class,
    ]);

  }

  public function installTables(int $round): void
  {
    if ($round == 1) {
      $this->getModel(Models\Report::class)->dropTableIfExists()->install();
    }
  }

  public function generateDemoData(): void
  {
    $mReport = $this->getModel(Models\Report::class);

    $mReport->record->recordCreate([
      'title' => 'Test report for Customers',
      'model' => \HubletoApp\Community\Customers\Models\Customer::class,
      'query' => '{}',
    ]);
  }

}
