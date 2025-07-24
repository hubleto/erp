<?php

namespace HubletoApp\Community\Reports;

class ReportManager
{
  public \HubletoMain\Loader $main;

  /** @var array<string, \HubletoMain\Core\Report> */
  protected array $reports = [];

  public function __construct(\HubletoMain\Loader $main)
  {
    $this->main = $main;
  }

  /**
   * Adds a report to the report manager
   *
   * @param \HubletoMain\Core\App $hubletoApp
   * @param string $reportClass
   * 
   * @return void
   * 
   */
  public function addReport(\HubletoMain\Core\App $hubletoApp, string $reportClass): void
  {
    $report = $this->main->di->create($reportClass);
    if ($report instanceof \HubletoMain\Core\Report) {
      $report->hubletoApp = $hubletoApp;
      $this->reports[$reportClass] = $report;
    }
  }

  /**
   * Get all reports registered in report manager.
   *
   * @return array<string, \HubletoMain\Core\Report>
   * 
   */
  public function getReports(): array
  {
    return $this->reports;
  }

  public function getReport(string $reportClass): \HubletoMain\Core\Report
  {
    return $this->reports[$reportClass];
  }

  public function getReportByUrlSlug(string $reportUrlSlug): null|\HubletoMain\Core\Report
  {
    foreach ($this->getReports() as $report) {
      if ($report->getUrlSlug() == $reportUrlSlug) {
        return $report;
      }
    }
    return null;
  }
}
