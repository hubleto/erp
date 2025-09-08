<?php declare(strict_types=1);

namespace Hubleto\Erp\Api;

use Exception;

class Search extends \Hubleto\Erp\Controllers\ApiController
{
  public function renderJson(): ?array
  {
    $results = [];

    $query = $this->router()->urlParamAsString('query');

    $expressions = [];
    foreach (explode(' ', strtr($query, ',;.', '   ')) as $e) {
      $expressions[] = trim($e);
    }

    $firstExpression = reset($expressions);

    if (count($expressions) == 1 && str_starts_with($firstExpression, '>')) {
      foreach ($this->appManager()->getEnabledApps() as $appNamespace => $app) {
        if (str_starts_with('>' . strtolower($app->shortName), strtolower($firstExpression))) {
          $results[] = [
            'label' => '>' . $app->shortName,
            // 'autocomplete' => '>' . $app->shortName,
            'url' => $app->manifest['rootUrlSlug'],
            'description' => 'Open ' . $app->shortName . ' app',
          ];
        }
      }
      array_shift($expressions);
    }

    if (count($expressions) == 1 && str_starts_with($firstExpression, '/')) {
      $searchSwitches = [];
      foreach ($this->appManager()->getEnabledApps() as $appNamespace => $app) {
        foreach ($app->searchSwitches as $switch => $name) {
          if (!isset($searchSwitches[$switch])) $searchSwitches[$switch] = [];
          $searchSwitches[$switch][] = $name;
        }
      }
      foreach ($searchSwitches as $switch => $names) {
        if (str_starts_with('/' . $switch, $firstExpression)) {
          $results[] = [
            'label' => '/' . $switch,
            'autocomplete' => '/' . $switch,
            'description' => 'Search in ' . join(' or ', $names),
          ];
        }
      }
      array_shift($expressions);
    }

    foreach ($this->appManager()->getEnabledApps() as $appNamespace => $app) {
      $canSearchThisApp = true;
      $expressionsToSearch = $expressions;
      foreach ($expressions as $key => $e) {
        if (str_starts_with($e, '>')) {
          unset($expressionsToSearch[$key]);
          if (!str_contains(strtolower($app->fullName), strtolower(str_replace('>', '', $e)))) {
            $canSearchThisApp = false;
          }
        }
        if (str_starts_with($e, '/')) {
          unset($expressionsToSearch[$key]);
          if (!$app->canHandleSearchSwith(trim($e, '/'))) {
            $canSearchThisApp = false;
          }
        }
      }

      if ($canSearchThisApp && count($expressionsToSearch) > 0) {
        $appResults = $app->search($expressionsToSearch);
        foreach ($appResults as $key => $value) {
          $value['APP_NAMESPACE'] = $appNamespace;
          $results[] = $value;
        }
      }
    }

    $results[] = [
      'label' => 'Help: How to use search',
      'url' => 'help/search',
    ];

    return $results;
  }
}
