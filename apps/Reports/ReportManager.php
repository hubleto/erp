<?php

namespace HubletoApp\Community\Reports;

class ReportManager extends \Hubleto\Framework\Core
{

  /** @var array<string, \HubletoMain\Report> */
  protected array $reports = [];

  /**
   * Adds a report to the report manager
   *
   * @param \Hubleto\Framework\App $hubletoApp
   * @param string $reportClass
   * 
   * @return void
   * 
   */
  public function addReport(\Hubleto\Framework\Interfaces\AppInterface $hubletoApp, string $reportClass): void
  {
    $report = $this->getService($reportClass);
    if ($report instanceof \HubletoMain\Report) {
      $report->hubletoApp = $hubletoApp;
      $this->reports[$reportClass] = $report;
    }
  }

  /**
   * Get all reports registered in report manager.
   *
   * @return array<string, \HubletoMain\Report>
   * 
   */
  public function getReports(): array
  {
    return $this->reports;
  }

  public function getReport(string $reportClass): \HubletoMain\Report
  {
    return $this->reports[$reportClass];
  }

  public function getReportByUrlSlug(string $reportUrlSlug): null|\HubletoMain\Report
  {
    foreach ($this->getReports() as $report) {
      if ($report->getUrlSlug() == $reportUrlSlug) {
        return $report;
      }
    }
    return null;
  }
}
