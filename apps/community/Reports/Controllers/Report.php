<?php

namespace HubletoApp\Community\Reports\Controllers;

class Report extends \HubletoMain\Core\Controller {

  public function prepareView(): void
  {
    parent::prepareView();

    $reportUrlSlug = $this->main->router->routeVarAsString('reportUrlSlug');
    $report = $this->main->reportManager->getReportByUrlSlug($reportUrlSlug);
    $reportData = $report->getReportData();

    $this->viewParams['report'] = $report;
    $this->viewParams['reportData'] = $reportData;

    $this->setView('@app/community/Reports/Views/Report.twig');
  }

}