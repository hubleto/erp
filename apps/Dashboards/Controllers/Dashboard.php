<?php

namespace Hubleto\App\Community\Dashboards\Controllers;

class Dashboard extends \Hubleto\Erp\Controller
{
  public function getBreadcrumbs(): array
  {
    $dashboardSlug = $this->router()->urlParamAsString('dashboardSlug');
    if (!empty($dashboardSlug)) {
      return array_merge(parent::getBreadcrumbs(), [
        [ 'url' => 'dashboards/' . $dashboardSlug, 'content' => $this->translate('Manage') ],
      ]);
    } else {
      return array_merge(parent::getBreadcrumbs(), []);
    }
  }

  public function prepareView(): void
  {
    parent::prepareView();

    $dashboardSlug = $this->router()->urlParamAsString('dashboardSlug');
    $mDashboard = $this->getModel(\Hubleto\App\Community\Dashboards\Models\Dashboard::class);

    $dashboard = $mDashboard->record->prepareReadQuery()
      ->where('slug', $dashboardSlug)
      ->with('PANELS')
      ->first()
      ?->toArray()
    ;

    $this->viewParams['dashboard'] = $dashboard;
    $this->viewParams['dashboardSlug'] = $dashboardSlug;

    $this->setView('@Hubleto:App:Community:Dashboards/Dashboard.twig');
  }

}
