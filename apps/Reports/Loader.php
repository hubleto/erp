<?php

namespace HubletoApp\Community\Reports;

class Loader extends \Hubleto\Framework\App
{
  public ReportManager $reportManager;

  public function __construct(\HubletoMain\Loader $main)
  {
    parent::__construct($main);
    $this->reportManager = $main->di->create(ReportManager::class);
  }

  public function init(): void
  {
    parent::init();

    $this->main->router->httpGet([
      '/^reports\/?$/' => Controllers\Reports::class,
      '/^reports\/(?<reportUrlSlug>.*?)\/?$/' => Controllers\Report::class,
    ]);

  }

  public function installTables(int $round): void
  {
    if ($round == 1) {
      (new Models\Report($this->main))->dropTableIfExists()->install();
    }
  }

  public function generateDemoData(): void
  {
    $mReport = $this->main->di->create(Models\Report::class);

    $mReport->record->recordCreate([
      'title' => 'Test report for Customers',
      'model' => \HubletoApp\Community\Customers\Models\Customer::class,
      'query' => '{}',
    ]);
  }

}
