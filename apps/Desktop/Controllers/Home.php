<?php

namespace Hubleto\App\Community\Desktop\Controllers;

use \Hubleto\App\Community\Dashboards\Loader as DashboardsApp;
use \Hubleto\App\Community\Dashboards\Models\Dashboard as DashboardModel;
use Hubleto\App\Community\Desktop\Loader;

class Home extends \Hubleto\Erp\Controller
{

  public bool $permittedForAllUsers = true;

  public const DID_YOU_KNOW = [
    "Press <span class='badge'>Ctrl+Space</span> to activate app launcher.",
    "Press <span class='badge'>Ctrl+S</span> to save any form.",
    "Press <span class='badge'>Esc</span> to close any form.",
    "Press <span class='badge'>Ctrl+K</span> to search whole Hubleto.",
    "Start Hubleto search with <span class='badge'>/i</span> switch to search invoices.",
    "Start Hubleto search with <span class='badge'>/d</span> switch to search deals.",
    "Start Hubleto search with <span class='badge'>/c</span> switch to search customers.",
    "Start Hubleto search with <span class='badge'>/p</span> switch to search projects.",
    "Order can have more than one project assigned.",
    "AI Assistent may be launched from any form by clicking on <i class='badge fas fa-wand-magic-sparkles mx-2'></i> icon.",
    "You can customize columns in tables by clicking on <i class='badge fas fa-ellipsis-vertical'></i> icon.",
    "You can create your custom Hubleto apps.",
    "Leads, Deals and Projects has their own calendars.",
    "Leads, Deals and Projects has their own tasks.",
    "You can have as many dashboard as you wish.",
    "Hubleto also has a dark mode.",
    "You can share records with other users.",
    "You can assign owner and manager to deals, projects, leads or others.",
    "Hubleto provides powerful and secure API to integrate with external services.",
    "Workflows are fully customizable.",
    "You can configure any number of email accounts to be monitored.",
    "Settings are very comprehensive. Take a look there.",
    "Hubleto is opensource.",
  ];

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

    /** @var Loader */
    $desktopApp = $this->getService(Loader::class);

    $enabledApps = $this->appManager()->getEnabledApps();
    $appsInSidebar = $desktopApp->getAppsInSidebar();

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

    $this->viewParams['didYouKnow'] = self::DID_YOU_KNOW[rand(0, count(self::DID_YOU_KNOW) - 1)];
    $this->viewParams['appsInSidebar'] = $appsInSidebar;
    $this->viewParams['welcomeScreenMessages'] = $welcomeScreenMessages;

    $this->setView('@Hubleto:App:Community:Desktop/Home.twig');
  }

}
