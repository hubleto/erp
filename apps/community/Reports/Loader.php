<?php

namespace HubletoApp\Community\Reports;

class Loader extends \HubletoMain\Core\App
{

  public ReportManager $reportManager;

  public function __construct(\HubletoMain $main)
  {
    parent::__construct($main);
    $this->reportManager = new ReportManager($main);
  }

  public function init(): void
  {
    parent::init();

    $this->main->router->httpGet([
      '/^reports\/?$/' => Controllers\Reports::class,
      '/^reports\/(?<reportUrlSlug>.*?)\/?$/' => Controllers\Report::class,
      // '/^reports\/(?<reportUrlSlug>.*?)\/load-data\/?$/' => Controllers\ReportLoadData::class,
      // '/^reports\/(?<reportUrlSlug>.*?)\/load-data\/?$/' => Controllers\ReportLoadData::class,
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
    $mReport = new Models\Report($this->main);

    $mReport->record->recordCreate([
      'title' => 'Test report for Customers',
      'model' => \HubletoApp\Community\Customers\Models\Customer::class,
      'query' => '{}',
    ]);
  }

}