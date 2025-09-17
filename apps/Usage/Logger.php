<?php

namespace Hubleto\App\Community\Usage;



class Logger extends \Hubleto\Framework\Core
{

  public function logUsage(string $message = ''): void
  {
    if ((bool) $this->getService(\Hubleto\Framework\AuthProvider::class)->getUserId()) {

      $urlParams = $this->router()->getUrlParams();
      $mLog = $this->getModel(Models\Log::class);

      $paramsStr = count($urlParams) == 0 ? '' : json_encode($urlParams);
      $mLog->record->recordCreate([
        'datetime' => date('Y-m-d H:i:s'),
        'ip' => $_SERVER['REMOTE_ADDR'] ?? '',
        'route' => trim($this->router()->getRoute(), '/'),
        'params' => strlen($paramsStr) < 255 ? $paramsStr : '',
        'message' => $message,
        'id_user' => $this->getService(\Hubleto\Framework\AuthProvider::class)->getUserId(),
      ]);
    }
  }

  public function getRecentlyUsedAppNamespaces(): array
  {

    $usedAppNamespaces = [];

    $mLog = $this->getModel(Models\Log::class);
    $usageLogs = $mLog->record
      ->where('id_user', $this->getService(\Hubleto\Framework\AuthProvider::class)->getUserId())
      ->where('datetime', '>=', date("Y-m-d", strtotime("-7 days")))
      ->orderBy('datetime', 'desc')
      ->get()
      ?->toArray()
    ;

    $installedAppsByUrlSlug = [];
    foreach ($this->appManager()->getEnabledApps() as $app) {
      $installedAppsByUrlSlug[$app->manifest['rootUrlSlug']] = $app;
    }

    foreach ($usageLogs as $log) {
      if (strpos($log['route'], '/') === false) {
        $rootUrlSlug = $log['route'];
      } else {
        $rootUrlSlug = substr($log['route'], 0, strpos($log['route'], '/'));
      }
      $usedApp = $installedAppsByUrlSlug[$rootUrlSlug] ?? null;
      if ($usedApp) {
        $usedAppNamespaces[] = $usedApp->namespace;
      }
    }

    return array_slice(array_unique($usedAppNamespaces), 0, 5);
  }

}
