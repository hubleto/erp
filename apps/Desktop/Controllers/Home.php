<?php

namespace HubletoApp\Community\Desktop\Controllers;

class Home extends \HubletoMain\Controller
{

  /**
   * Inits the app: adds routes, settings, calendars, hooks, menu items, ...
   *
   * @return void
   * 
   */
  public function init(): void
  {
  }

  public function prepareView(): void
  {
    parent::prepareView();

    $dashboardsApp = $this->getAppManager()->getApp(\HubletoApp\Community\Dashboards\Loader::class);
    if ($dashboardsApp) {
      $mDashboard = $this->getModel(\HubletoApp\Community\Dashboards\Models\Dashboard::class);

      $defaultDashboard = $mDashboard->record->prepareReadQuery()
        ->where('is_default', true)
        ->with('PANELS')
        ->first()
        ?->toArray();
      ;

      $this->viewParams['defaultDashboard'] = $defaultDashboard;

    }

    $this->setView('@HubletoApp:Community:Desktop/Home.twig');
  }

}
