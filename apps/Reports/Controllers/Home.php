<?php

namespace Hubleto\App\Community\Reports\Controllers;

class Home extends \Hubleto\Erp\Controller
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

    /** @var \Hubleto\App\Community\Reports\Loader $reportsApp */
    $reportsApp = $this->appManager()->getApp(\Hubleto\App\Community\Reports\Loader::class);
    $reports = $reportsApp->reportManager->getReports();
    $this->viewParams['reports'] = $reports;

    $this->setView('@Hubleto:App:Community:Reports/Home.twig');
  }

}
