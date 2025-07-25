<?php

namespace HubletoApp\Community\Desktop\Controllers;

class Desktop extends \HubletoMain\Controller
{
  public bool $disableLogUsage = true;

  public function prepareView(): void
  {
    parent::prepareView();

    $appsInSidebar = $this->main->apps->getEnabledApps();
    $activatedApp = null;

    foreach ($appsInSidebar as $appNamespace => $app) {
      if (
        !$this->main->permissions->isAppPermittedForActiveUser($app)
        || $app->configAsInteger('sidebarOrder') <= 0
      ) {
        unset($appsInSidebar[$appNamespace]);
      }
      if ($app->isActivated) {
        $activatedApp = $app;
      }
    }

    if ($activatedApp === null) {
      $activatedApp = $this->main->apps->community('Desktop');
    }

    uasort($appsInSidebar, function ($a, $b) {
      $aOrder = $a->configAsInteger('sidebarOrder');
      $bOrder = $b->configAsInteger('sidebarOrder');
      return $aOrder <=> $bOrder;
    });

    $this->viewParams['appsInSidebar'] = $appsInSidebar;
    $this->viewParams['activatedApp'] = $activatedApp;
    $this->viewParams['sidebarGroups'] = $this->main->config->getAsArray('sidebarGroups', [
      'crm' => [ 'title' => $this->translate('CRM'), 'icon' => 'fas fa-id-card-clip' ],
      'tasks' => [ 'title' => $this->translate('Tasks'), 'icon' => 'fas fa-list-check' ],
      'documents' => [ 'title' => $this->translate('Documents'), 'icon' => 'fas fa-file' ],
      'marketing' => [ 'title' => $this->translate('Marketing'), 'icon' => 'fas fa-bullseye' ],
      'sales' => [ 'title' => $this->translate('Sales'), 'icon' => 'fas fa-users-viewfinder' ],
      'communication' => [ 'title' => $this->translate('Communication'), 'icon' => 'fas fa-comments' ],
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

    $this->viewParams['availableLanguages'] = $this->main->config->getAsArray('availableLanguages', [
      "en" => [ "flagImage" => "assets/images/flags/en.jpg", "name" => "English" ],
      "de" => [ "flagImage" => "assets/images/flags/de.jpg", "name" => "Deutsch" ],
      "es" => [ "flagImage" => "assets/images/flags/es.jpg", "name" => "Español" ],
      "fr" => [ "flagImage" => "assets/images/flags/fr.jpg", "name" => "Francais" ],
      "it" => [ "flagImage" => "assets/images/flags/it.jpg", "name" => "Italiano" ],
      "pl" => [ "flagImage" => "assets/images/flags/pl.jpg", "name" => "Polski" ],
      "ro" => [ "flagImage" => "assets/images/flags/ro.jpg", "name" => "Română" ],
      "cs" => [ "flagImage" => "assets/images/flags/cs.jpg", "name" => "Česky" ],
      "sk" => [ "flagImage" => "assets/images/flags/sk.jpg", "name" => "Slovensky" ],
    ]);

    $appMenu = $this->main->apps->community('Desktop')->appMenu;
    $this->viewParams['appMenu'] = $appMenu->getItems();

    $this->setView('@HubletoApp:Community:Desktop/Desktop.twig');
  }

}
