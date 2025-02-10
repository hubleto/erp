<?php

namespace HubletoApp\Community\Reports\Controllers;

class Report extends \HubletoMain\Core\Controller {

  public function prepareView(): void
  {
    parent::prepareView();

    $reportUrlSlug = $this->main->router->routeVarAsString('reportUrlSlug');
    $report = $this->main->reportManager->getReportByUrlSlug($reportUrlSlug);
    $reportConfig = $report->getReportConfig();

    $this->viewParams['report'] = $report;
    $this->viewParams['reportConfig'] = $reportConfig;

    $this->setView('@app/community/Reports/Views/Report.twig');
  }

}