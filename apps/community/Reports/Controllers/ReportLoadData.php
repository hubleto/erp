<?php

namespace HubletoApp\Community\Reports\Controllers;

class ReportLoadData extends \HubletoMain\Core\Controllers\Controller {
  public int $returnType = \ADIOS\Core\Controller::RETURN_TYPE_JSON;
  public bool $permittedForAllUsers = true;

  public function renderJson(): array
  {
    $reportUrlSlug = $this->main->router->routeVarAsString('reportUrlSlug');
    $report = $this->main->apps->community('Reports')->reportManager->getReportByUrlSlug($reportUrlSlug);
    return $report->loadData();
  }

}