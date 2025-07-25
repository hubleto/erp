<?php

namespace HubletoApp\Community\Reports\Controllers;

class Report extends \HubletoMain\Controller
{
  public function getBreadcrumbs(): array
  {
    $reportUrlSlug = $this->main->router->routeVarAsString('reportUrlSlug');
    $report = $this->main->apps->community('Reports')->reportManager->getReportByUrlSlug($reportUrlSlug);

    return array_merge(parent::getBreadcrumbs(), [
      [ 'url' => 'reports', 'content' => $this->translate('Reports') ],
      [ 'url' => '', 'content' => $report->name ],
    ]);
  }

  public function prepareView(): void
  {
    parent::prepareView();

    $reportUrlSlug = $this->main->router->routeVarAsString('reportUrlSlug');
    $report = $this->main->apps->community('Reports')->reportManager->getReportByUrlSlug($reportUrlSlug);
    // $reportConfig = $report->getConfig();

    $this->viewParams['report'] = $report;
    // $this->viewParams['reportConfig'] = $reportConfig;

    $this->setView('@HubletoApp:Community:Reports/Report.twig');
  }

}
