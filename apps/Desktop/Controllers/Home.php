<?php

namespace Hubleto\App\Community\Desktop\Controllers;

use \Hubleto\App\Community\Dashboards\Loader as DashboardsApp;
use \Hubleto\App\Community\Dashboards\Models\Dashboard as DashboardModel;

class Home extends \Hubleto\Erp\Controller
{

  public bool $permittedForAllUsers = true;

  /**
   * Inits the app: adds routes, settings, calendars, event listeners, menu items, ...
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

    $enabledApps = $this->appManager()->getEnabledApps();

    $dashboardsApp = $this->appManager()->getApp(DashboardsApp::class);
    if ($dashboardsApp) {
      /** @var DashboardModel */
      $mDashboard = $this->getModel(DashboardModel::class);
      $this->viewParams['defaultDashboard'] = $mDashboard->getDefaultDashboard();

    }

    $welcomeScreenMessages = [];
    foreach ($enabledApps as $appNamespace => $app) {
      try {
        $welcomeScreenMessages = array_merge(
          $welcomeScreenMessages,
          $app->getWelcomeScreenMessages()
        );
      } catch (\Throwable $e) {
        //
      }
    }

    $this->viewParams['welcomeScreenMessages'] = $welcomeScreenMessages;

    $this->setView('@Hubleto:App:Community:Desktop/Home.twig');
  }

}
