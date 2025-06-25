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
    }

    uasort($appsInSidebar, function($a, $b) {
      $aOrder = $a->configAsInteger('sidebarOrder');
      $bOrder = $b->configAsInteger('sidebarOrder');
      return $aOrder <=> $bOrder;
    });

    $this->viewParams['appsInSidebar'] = $appsInSidebar;
    $this->viewParams['activatedApp'] = $activatedApp;
    $this->viewParams['sidebarGroups'] = [
      'basic' => [ 'title' => $this->translate('Home'), 'icon' => 'fas fa-home' ],
      'crm' => [ 'title' => $this->translate('CRM'), 'icon' => 'fas fa-users-viewfinder' ],
      'finance' => [ 'title' => $this->translate('Finance'), 'icon' => 'fas fa-credit-card' ],
      'supply-chain' => [ 'title' => $this->translate('Supply chain'), 'icon' => 'fas fa-truck' ],
      'e-commerce' => [ 'title' => $this->translate('E-Commerce'), 'icon' => 'fas fa-cart-shopping' ],
      'reporting' => [ 'title' => $this->translate('Reporting'), 'icon' => 'fas fa-chart-line' ],
      'settings' => [ 'title' => $this->translate('Settings'), 'icon' => 'fas fa-cog' ],
      'help' => [ 'title' => $this->translate('Help'), 'icon' => 'fas fa-life-ring' ],
      'custom' => [ 'title' => $this->translate('Custom'), 'icon' => 'fas fa-puzzle-piece' ],
    ];

    $appMenu = $this->main->apps->community('Desktop')->appMenu;
    $this->viewParams['appMenu'] = $appMenu->getItems();

    $this->setView('@HubletoApp:Community:Desktop/Desktop.twig');
  }

}