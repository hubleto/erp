<?php

namespace HubletoApp\Community\Reports\Controllers;

class Home extends \HubletoMain\Controller
{
  public function getBreadcrumbs(): array
  {
    return array_merge(parent::getBreadcrumbs(), [
      [ 'url' => 'reports', 'content' => $this->translate('Reports') ],
    ]);
  }

  public function prepareView(): void
  {
    parent::prepareView();

    $reports = $this->main->apps->community('Reports')->reportManager->getReports();
    $this->viewParams['reports'] = $reports;

    $this->setView('@HubletoApp:Community:Reports/Home.twig');
  }

}
