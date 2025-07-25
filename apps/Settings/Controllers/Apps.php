<?php

namespace HubletoApp\Community\Settings\Controllers;

class Apps extends \HubletoMain\Controller
{
  public function getBreadcrumbs(): array
  {
    return array_merge(parent::getBreadcrumbs(), [
      [ 'url' => 'settings', 'content' => $this->translate('Settings') ],
      [ 'url' => 'apps', 'content' => $this->translate('Apps') ],
    ]);
  }

  public function prepareView(): void
  {
    parent::prepareView();

    $installApp = $this->main->urlParamAsString('installApp');
    $disableApp = $this->main->urlParamAsString('disableApp');
    $enableApp = $this->main->urlParamAsString('enableApp');
    $findApp = $this->main->urlParamAsString('findApp');

    if (!empty($installApp) && !$this->main->apps->isAppInstalled($installApp)) {
      $this->main->apps->installApp(1, $installApp, [], true);
      $this->main->apps->installApp(2, $installApp, [], true);
      $this->main->apps->installApp(3, $installApp, [], true);
      $this->main->router->redirectTo('');
    }

    if (!empty($disableApp) && $this->main->apps->isAppInstalled($disableApp)) {
      $this->main->apps->disableApp($disableApp);
      $this->main->router->redirectTo('');
    }

    if (!empty($enableApp) && $this->main->apps->isAppInstalled($enableApp)) {
      $this->main->apps->enableApp($enableApp);
      $this->main->router->redirectTo('');
    }

    $installedApps = array_merge($this->main->apps->getEnabledApps(), $this->main->apps->getDisabledApps());
    $availableApps = $this->main->apps->getAvailableApps();

    $appsToShow = [];
    if (empty($findApp)) {
      foreach ($installedApps as $appNamespace => $app) {
        $appsToShow[$appNamespace] = [
          'manifest' => $app->manifest,
          'instance' => $app,
            'type' => $app->manifest['appType'],
        ];
      }
    } else {
      $appsFound = array_filter($availableApps, function ($appManifest, $appNamespace) use ($findApp) {
        return \str_contains(strtolower($appNamespace), strtolower($findApp));
      }, ARRAY_FILTER_USE_BOTH);

      foreach ($appsFound as $appNamespace => $appManifest) {
        if (isset($installedApps[$appNamespace])) {
          $appsToShow[$appNamespace] = [
            'manifest' => $installedApps[$appNamespace]->manifest,
            'instance' => $installedApps[$appNamespace],
            'type' => $installedApps[$appNamespace]->manifest['appType'],
          ];
        } else {
          $appsToShow[$appNamespace] = [
            'manifest' => $appManifest,
            'instance' => null,
            'type' => $appManifest['appType'],
          ];
        }
      }
    }

    $this->viewParams['findApp'] = $findApp;
    $this->viewParams['installedApps'] = $installedApps;
    $this->viewParams['availableApps'] = $availableApps;
    $this->viewParams['appsToShow'] = $appsToShow;

    $this->setView('@HubletoApp:Community:Settings/Apps.twig');
  }

}
