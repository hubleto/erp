<?php

namespace Hubleto\App\Community\Reports\Controllers;

class Report extends \Hubleto\Erp\Controller
{
  public function getBreadcrumbs(): array
  {
    $reportUrlSlug = $this->getRouter()->routeVarAsString('reportUrlSlug');
    $report = $this->getAppManager()->getApp(\Hubleto\App\Community\Reports\Loader::class)->reportManager->getReportByUrlSlug($reportUrlSlug);

    return array_merge(parent::getBreadcrumbs(), [
      [ 'url' => 'reports', 'content' => $this->translate('Reports') ],
      [ 'url' => '', 'content' => $report->name ],
    ]);
  }

  public function prepareView(): void
  {
    parent::prepareView();

    $reportUrlSlug = $this->getRouter()->routeVarAsString('reportUrlSlug');
    $report = $this->getAppManager()->getApp(\Hubleto\App\Community\Reports\Loader::class)->reportManager->getReportByUrlSlug($reportUrlSlug);
    // $reportConfig = $report->getConfig();

    $this->viewParams['report'] = $report;
    // $this->viewParams['reportConfig'] = $reportConfig;

    $this->setView('@Hubleto:App:Community:Reports/Report.twig');
  }

}
