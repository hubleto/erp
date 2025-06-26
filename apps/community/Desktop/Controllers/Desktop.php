<?php

namespace HubletoApp\Community\Desktop\Controllers;

class Desktop extends \HubletoMain\Core\Controllers\Controller
{

  public bool $disableLogUsage = true;

  public function prepareView(): void
  {
    parent::prepareView();

    $appsInSidebar = $this->main->apps->getEnabledApps();
    $activatedApp = null;

    foreach ($appsInSidebar as $appNamespace => $app) {
      if ($app->configAsInteger('sidebarOrder') <= 0) {
        unset($appsInSidebar[$appNamespace]);
      }
      if ($app->isActivated) {
        $activatedApp = $app;
      }
    }

    if ($activatedApp === null) $activatedApp = $this->main->apps->community('Desktop');

    uasort($appsInSidebar, function($a, $b) {
      $aOrder = $a->configAsInteger('sidebarOrder');
      $bOrder = $b->configAsInteger('sidebarOrder');
      return $aOrder <=> $bOrder;
    });

    $this->viewParams['appsInSidebar'] = $appsInSidebar;
    $this->viewParams['activatedApp'] = $activatedApp;
    $this->viewParams['sidebarGroups'] = [
      'crm' => [ 'title' => $this->translate('CRM'), 'icon' => 'fas fa-id-card-clip' ],
      'documents' => [ 'title' => $this->translate('Documents'), 'icon' => 'fas fa-file' ],
      'sales' => [ 'title' => $this->translate('Sales'), 'icon' => 'fas fa-users-viewfinder' ],
      'communication' => [ 'title' => $this->translate('Communication'), 'icon' => 'fas fa-comments' ],
      'projects' => [ 'title' => $this->translate('Projects'), 'icon' => 'fas fa-diagram-project' ],
      'supply-chain' => [ 'title' => $this->translate('Supply chain'), 'icon' => 'fas fa-truck' ],
      'helpdesk' => [ 'title' => $this->translate('Helpdesk'), 'icon' => 'fas fa-headset' ],
      'events' => [ 'title' => $this->translate('Events'), 'icon' => 'fas fa-people-group' ],
      'e-commerce' => [ 'title' => $this->translate('E-Commerce'), 'icon' => 'fas fa-cart-shopping' ],
      'finance' => [ 'title' => $this->translate('Finance'), 'icon' => 'fas fa-credit-card' ],
      'reporting' => [ 'title' => $this->translate('Reporting'), 'icon' => 'fas fa-chart-line' ],
      'maintenance' => [ 'title' => $this->translate('Maintenance'), 'icon' => 'fas fa-cog' ],
      'help' => [ 'title' => $this->translate('Help'), 'icon' => 'fas fa-life-ring' ],
      'custom' => [ 'title' => $this->translate('Custom'), 'icon' => 'fas fa-puzzle-piece' ],
    ];

    $appMenu = $this->main->apps->community('Desktop')->appMenu;
    $this->viewParams['appMenu'] = $appMenu->getItems();

    $this->setView('@HubletoApp:Community:Desktop/Desktop.twig');
  }

}