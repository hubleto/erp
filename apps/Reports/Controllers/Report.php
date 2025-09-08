<?php

namespace Hubleto\App\Community\Reports\Controllers;

class Report extends \Hubleto\Erp\Controller
{
  public function getBreadcrumbs(): array
  {
    $reportUrlSlug = $this->router()->routeVarAsString('reportUrlSlug');
    $report = $this->appManager()->getApp(\Hubleto\App\Community\Reports\Loader::class)->reportManager->getReportByUrlSlug($reportUrlSlug);

    return array_merge(parent::getBreadcrumbs(), [
      [ 'url' => 'reports', 'content' => $this->translate('Reports') ],
      [ 'url' => '', 'content' => $report->name ],
    ]);
  }

  public function prepareView(): void
  {
    parent::prepareView();

    $reportUrlSlug = $this->router()->routeVarAsString('reportUrlSlug');
    $report = $this->appManager()->getApp(\Hubleto\App\Community\Reports\Loader::class)->reportManager->getReportByUrlSlug($reportUrlSlug);
    // $reportConfig = $report->config();

    $this->viewParams['report'] = $report;
    // $this->viewParams['reportConfig'] = $reportConfig;

    $this->setView('@Hubleto:App:Community:Reports/Report.twig');
  }

}
