<?php

namespace Hubleto\App\Community\Reports\Controllers;

class ReportLoadData extends \Hubleto\Erp\Controllers\ApiController
{
  public function renderJson(): array
  {
    $reportUrlSlug = $this->router()->routeVarAsString('reportUrlSlug');
    $report = $this->appManager()->getApp(\Hubleto\App\Community\Reports\Loader::class)->reportManager->getReportByUrlSlug($reportUrlSlug);
    return $report->loadData();
  }

}
