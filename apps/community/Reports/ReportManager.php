<?php

namespace HubletoApp\Community\Reports;

class ReportManager
{

  public \HubletoMain $main;

  /** @var array<string, \HubletoMain\Core\Report> */
  protected array $reports = [];

  public function __construct(\HubletoMain $main)
  {
    $this->main = $main;

    // $reports = \ADIOS\Core\Helper::scanDirRecursively(__DIR__ . '/../reports');
    // foreach ($reports as $report) {
    //   $reportClass = '\\HubletoMain\\Report\\' . str_replace('/', '\\', $report);
    //   $reportClass = str_replace('.php', '', $reportClass);
    //   $this->addReport($this->main->apps->community('Reports'), $reportClass);
    // }
  }

  public function addReport(\HubletoMain\Core\App $hubletoApp, string $reportClass): void
  {
    $report = new $reportClass($hubletoApp, $this->main);
    if ($report instanceof \HubletoMain\Core\Report) {
      $this->reports[$reportClass] = $report;
    }
  }

  /** @return array<string, \HubletoMain\Core\Report> */
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