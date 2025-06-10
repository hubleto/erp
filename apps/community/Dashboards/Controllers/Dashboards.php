<?php

namespace HubletoApp\Community\Dashboards\Controllers;

class Dashboards extends \HubletoMain\Core\Controllers\Controller
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

    $mDashboard = new \HubletoApp\Community\Dashboards\Models\Dashboard($this->main);

    $this->viewParams['dashboardSlug'] = $this->main->urlParamAsString('dashboardSlug');

    $dashboards = $mDashboard->record->prepareReadQuery()
      ->with('PANELS')
      ->get()
      ?->toArray();
    ;

    foreach ($dashboards as $key1 => $dashboard) {
      foreach ($dashboard['PANELS'] as $key2 => $panel) {
        $dashboards[$key1]['PANELS'][$key2]['boardUrlSlug'] = $panel['board_url_slug'];
      }
    }

    $this->viewParams['dashboards'] = $dashboards;

    $this->setView('@HubletoApp:Community:Dashboards/Dashboards.twig');
  }

}