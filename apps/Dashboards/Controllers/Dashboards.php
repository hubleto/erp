<?php

namespace Hubleto\App\Community\Dashboards\Controllers;

use Hubleto\App\Community\Auth\AuthProvider;

class Dashboards extends \Hubleto\Erp\Controller
{
  public function getBreadcrumbs(): array
  {
    return array_merge(parent::getBreadcrumbs(), [
      [ 'url' => 'dashboards', 'content' => $this->translate('Dashboards') ],
    ]);
  }

  public function prepareView(): void
  {
    parent::prepareView();

    $mDashboard = $this->getModel(\Hubleto\App\Community\Dashboards\Models\Dashboard::class);

    $dashboardSlug = $this->router()->urlParamAsString('dashboardSlug');

    $dashboards = $mDashboard->record->prepareReadQuery()
      ->where('id_owner', $this->getService(AuthProvider::class)->getUserId())
      ->with('PANELS')
      ->get()
      ?->toArray();
    ;

    // if (empty($dashboardSlug)) {
    //   $tmp = reset($dashboards);
    //   $dashboardSlug = $tmp['slug'] ?? '';
    // }

    $this->viewParams['dashboards'] = $dashboards;
    $this->viewParams['dashboardSlug'] = $dashboardSlug;

    $this->setView('@Hubleto:App:Community:Dashboards/Dashboards.twig');
  }

}
