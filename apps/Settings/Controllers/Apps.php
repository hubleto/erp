<?php

namespace Hubleto\App\Community\Settings\Controllers;

class Apps extends \Hubleto\Erp\Controller
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

    $installApp = $this->router()->urlParamAsString('installApp');
    $disableApp = $this->router()->urlParamAsString('disableApp');
    $enableApp = $this->router()->urlParamAsString('enableApp');
    $findApp = $this->router()->urlParamAsString('findApp');

    $appManager = $this->appManager();

    if (!empty($installApp) && !$appManager->isAppInstalled($installApp)) {
      $appManager->installApp(1, $installApp, [], true);
      $appManager->installApp(2, $installApp, [], true);
      $appManager->installApp(3, $installApp, [], true);
      $this->router()->redirectTo('');
    }

    if (!empty($disableApp) && $appManager->isAppInstalled($disableApp)) {
      $appManager->disableApp($disableApp);
      $this->router()->redirectTo('');
    }

    if (!empty($enableApp) && $appManager->isAppInstalled($enableApp)) {
      $appManager->enableApp($enableApp);
      $this->router()->redirectTo('');
    }

    $installedApps = array_merge($appManager->getEnabledApps(), $appManager->getDisabledApps());
    $availableApps = $appManager->getAvailableApps();

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

    $this->setView('@Hubleto:App:Community:Settings/Apps.twig');
  }

}
