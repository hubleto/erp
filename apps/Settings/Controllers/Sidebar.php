<?php

namespace HubletoApp\Community\Settings\Controllers;

class Sidebar extends \HubletoMain\Controller
{
  public function getBreadcrumbs(): array
  {
    return array_merge(parent::getBreadcrumbs(), [
      [ 'url' => 'settings', 'content' => $this->translate('Settings') ],
      [ 'url' => 'sidebar', 'content' => $this->translate('Sidebar') ],
    ]);
  }

  public function init(): void
  {
    $installedApps = array_merge($this->main->apps->getEnabledApps(), $this->main->apps->getDisabledApps());

    if ($this->main->urlParamAsBool("save")) {
      $appSidebarSettings = $this->main->urlParamAsArray('app');

      foreach ($appSidebarSettings as $rootUrlSlug => $sidebarOrder) {
        foreach ($installedApps as $appNamespace => $app) {
          if (($app->manifest['rootUrlSlug'] ?? '') == $rootUrlSlug) {
            $app->saveConfig('sidebarOrder', $sidebarOrder);
            $app->setConfigAsString('sidebarOrder', $sidebarOrder);
          }
        }
      }
    }

  }

  public function prepareView(): void
  {
    parent::prepareView();

    $installedApps = array_merge($this->main->apps->getEnabledApps(), $this->main->apps->getDisabledApps());

    uasort($installedApps, function ($a, $b) {
      $aOrder = $a->configAsInteger('sidebarOrder');
      $bOrder = $b->configAsInteger('sidebarOrder');
      return $aOrder <=> $bOrder;
    });

    $this->viewParams['installedApps'] = $installedApps;

    $this->setView('@HubletoApp:Community:Settings/Sidebar.twig');
  }

}
