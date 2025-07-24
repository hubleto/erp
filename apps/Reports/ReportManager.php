<?php

namespace HubletoApp\Community\Reports;

class ReportManager
{
  public \HubletoMain\Loader $main;

  /** @var array<string, \Hubleto\Framework\Report> */
  protected array $reports = [];

  public function __construct(\HubletoMain\Loader $main)
  {
    $this->main = $main;
  }

  /**
   * Adds a report to the report manager
   *
   * @param \Hubleto\Framework\App $hubletoApp
   * @param string $reportClass
   * 
   * @return void
   * 
   */
  public function addReport(\Hubleto\Framework\App $hubletoApp, string $reportClass): void
  {
    $report = $this->main->di->create($reportClass);
    if ($report instanceof \Hubleto\Framework\Report) {
      $report->hubletoApp = $hubletoApp;
      $this->reports[$reportClass] = $report;
    }
  }

  /**
   * Get all reports registered in report manager.
   *
   * @return array<string, \Hubleto\Framework\Report>
   * 
   */
  public function getReports(): array
  {
    return $this->reports;
  }

  public function getReport(string $reportClass): \Hubleto\Framework\Report
  {
    return $this->reports[$reportClass];
  }

  public function getReportByUrlSlug(string $reportUrlSlug): null|\Hubleto\Framework\Report
  {
    foreach ($this->getReports() as $report) {
      if ($report->getUrlSlug() == $reportUrlSlug) {
        return $report;
      }
    }
    return null;
  }
}
