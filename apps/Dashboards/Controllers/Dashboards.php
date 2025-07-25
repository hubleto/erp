<?php

namespace HubletoApp\Community\Dashboards\Controllers;

class Dashboards extends \HubletoMain\Controller
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

    $mDashboard = $this->main->di->create(\HubletoApp\Community\Dashboards\Models\Dashboard::class);

    $dashboardSlug = $this->main->urlParamAsString('dashboardSlug');

    $dashboards = $mDashboard->record->prepareReadQuery()
      ->where('id_owner', $this->main->auth->getUserId())
      ->with('PANELS')
      ->get()
      ?->toArray();
    ;

    if (empty($dashboardSlug)) {
      $tmp = reset($dashboards);
      $dashboardSlug = $tmp['slug'] ?? '';
    }

    $this->viewParams['dashboards'] = $dashboards;
    $this->viewParams['dashboardSlug'] = $dashboardSlug;

    $this->setView('@HubletoApp:Community:Dashboards/Dashboards.twig');
  }

}
