<?php

namespace Hubleto\App\Community\Desktop\Controllers;

use Hubleto\App\Community\Auth\AuthProvider;
use Hubleto\App\Community\Settings\PermissionsManager;

class Desktop extends \Hubleto\Erp\Controller
{
  public bool $disableLogUsage = true;

  public function prepareView(): void
  {
    parent::prepareView();

    $appsInSidebar = $this->appManager()->getEnabledApps();
    $activatedApp = null;

    foreach ($appsInSidebar as $appNamespace => $app) {
      if (
        !$this->getService(PermissionsManager::class)->isAppPermittedForActiveUser($app)
        || $app->configAsInteger('sidebarOrder') <= 0
      ) {
        unset($appsInSidebar[$appNamespace]);
      }
      if ($app->isActivated) {
        $activatedApp = $app;
      }
    }

    if ($activatedApp === null) {
      $activatedApp = $this->appManager()->getApp(\Hubleto\App\Community\Desktop\Loader::class);
    }

    uasort($appsInSidebar, function ($a, $b) {
      $aOrder = $a->configAsInteger('sidebarOrder');
      $bOrder = $b->configAsInteger('sidebarOrder');
      return $aOrder <=> $bOrder;
    });

    $this->viewParams['appsInSidebar'] = $appsInSidebar;
    $this->viewParams['activatedApp'] = $activatedApp;
    $this->viewParams['sidebarGroups'] = $this->config()->getAsArray('sidebarGroups', [
      'addressbook' => [ 'title' => $this->translate('Addressbook'), 'icon' => 'fas fa-id-card-clip' ],
      'calendar' => [ 'title' => $this->translate('Calendar'), 'icon' => 'fas fa-calendar' ],
      // 'productivity' => [ 'title' => $this->translate('Productivity'), 'icon' => 'fas fa-list-check' ],
      'documents' => [ 'title' => $this->translate('Documents'), 'icon' => 'fas fa-file' ],
      'communication' => [ 'title' => $this->translate('Communication'), 'icon' => 'fas fa-comments' ],
      'workflow' => [ 'title' => $this->translate('Workflow'), 'icon' => 'fas fa-diagram-project' ],
      'marketing' => [ 'title' => $this->translate('Marketing'), 'icon' => 'fas fa-bullseye' ],
      'sales' => [ 'title' => $this->translate('Sales'), 'icon' => 'fas fa-users-viewfinder' ],
      'projects' => [ 'title' => $this->translate('Projects'), 'icon' => 'fas fa-diagram-project' ],
      'supply-chain' => [ 'title' => $this->translate('Supply chain'), 'icon' => 'fas fa-truck' ],
      'helpdesk' => [ 'title' => $this->translate('Helpdesk'), 'icon' => 'fas fa-headset' ],
      'events' => [ 'title' => $this->translate('Events'), 'icon' => 'fas fa-people-group' ],
      'e-commerce' => [ 'title' => $this->translate('E-Commerce'), 'icon' => 'fas fa-cart-shopping' ],
      'website' => [ 'title' => $this->translate('Website'), 'icon' => 'fas fa-globe' ],
      'finance' => [ 'title' => $this->translate('Finance'), 'icon' => 'fas fa-credit-card' ],
      'reporting' => [ 'title' => $this->translate('Reporting'), 'icon' => 'fas fa-chart-line' ],
      'maintenance' => [ 'title' => $this->translate('Maintenance'), 'icon' => 'fas fa-cog' ],
      'help' => [ 'title' => $this->translate('Help'), 'icon' => 'fas fa-life-ring' ],
      'custom' => [ 'title' => $this->translate('Custom'), 'icon' => 'fas fa-puzzle-piece' ],
    ]);

    $this->viewParams['availableLanguages'] = $this->config()->getAsArray('availableLanguages', [
      "en" => [ "flagImage" => "en.jpg", "name" => "English" ],
      "de" => [ "flagImage" => "de.jpg", "name" => "Deutsch" ],
      "es" => [ "flagImage" => "es.jpg", "name" => "Español" ],
      "fr" => [ "flagImage" => "fr.jpg", "name" => "Francais" ],
      "it" => [ "flagImage" => "it.jpg", "name" => "Italiano" ],
      "pl" => [ "flagImage" => "pl.jpg", "name" => "Polski" ],
      "ro" => [ "flagImage" => "ro.jpg", "name" => "Română" ],
      "cs" => [ "flagImage" => "cs.jpg", "name" => "Česky" ],
      "sk" => [ "flagImage" => "sk.jpg", "name" => "Slovensky" ],
    ]);

    $appMenu = $this->getService(\Hubleto\App\Community\Desktop\Loader::class)->appMenu;
    $this->viewParams['appMenu'] = [];
    foreach ($appMenu as $item) {
      if ($item['app'] === $activatedApp) {
        $this->viewParams['appMenu'][] = $item;
      }
    }

    /** @var AuthProvider $authProvider */
    $authProvider = $this->getService(AuthProvider::class);
    $this->viewParams['user'] = $authProvider->getUserFromSession();

    $this->setView('@Hubleto:App:Community:Desktop/Desktop.twig');
  }

}
