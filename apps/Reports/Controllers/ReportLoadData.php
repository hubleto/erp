<?php

namespace HubletoApp\Community\Reports\Controllers;

class ReportLoadData extends \HubletoMain\Controllers\ApiController
{
  public function renderJson(): array
  {
    $reportUrlSlug = $this->main->router->routeVarAsString('reportUrlSlug');
    $report = $this->main->apps->community('Reports')->reportManager->getReportByUrlSlug($reportUrlSlug);
    return $report->loadData();
  }

}
