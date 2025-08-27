<?php

namespace HubletoApp\Community\Reports\Controllers;

class ReportLoadData extends \Hubleto\Erp\Controllers\ApiController
{
  public function renderJson(): array
  {
    $reportUrlSlug = $this->getRouter()->routeVarAsString('reportUrlSlug');
    $report = $this->getAppManager()->getApp(\HubletoApp\Community\Reports\Loader::class)->reportManager->getReportByUrlSlug($reportUrlSlug);
    return $report->loadData();
  }

}
