<?php

namespace Hubleto\App\Community\Desktop\Controllers;

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

    $dashboardsApp = $this->appManager()->getApp(\Hubleto\App\Community\Dashboards\Loader::class);
    if ($dashboardsApp) {
      $mDashboard = $this->getModel(\Hubleto\App\Community\Dashboards\Models\Dashboard::class);

      $defaultDashboard = $mDashboard->record->prepareReadQuery()
        ->where('is_default', true)
        ->with('PANELS')
        ->first()
        ?->toArray();
      ;

      $this->viewParams['defaultDashboard'] = $defaultDashboard;

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
