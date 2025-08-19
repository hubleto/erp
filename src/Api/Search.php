<?php declare(strict_types=1);

namespace HubletoMain\Api;

use Exception;

class Search extends \HubletoMain\Controllers\ApiController
{
  public function renderJson(): ?array
  {
    $results = [];

    $query = $this->main->urlParamAsString('query');

    $expressions = [];
    foreach (explode(' ', strtr($query, ',;.', '   ')) as $e) {
      $expressions[] = trim($e);
    }

    $firstExpression = reset($expressions);

    if (count($expressions) == 1 && str_starts_with($firstExpression, '>')) {
      foreach ($this->main->apps->getEnabledApps() as $appNamespace => $app) {
        if (str_starts_with('>' . $app->shortName, $firstExpression)) {
          $results[] = [
            'label' => '>' . $app->shortName,
            'autocomplete' => '>' . $app->shortName,
          ];
        }
      }
      array_shift($expressions);
    }

    if (count($expressions) == 1 && str_starts_with($firstExpression, '/')) {
      $searchSwitches = [];
      foreach ($this->main->apps->getEnabledApps() as $appNamespace => $app) {
        $searchSwitches = array_merge($searchSwitches, $app->searchSwitches);
      }
      $searchSwitches = array_unique($searchSwitches);
      foreach ($searchSwitches as $switch) {
        if (str_starts_with('/' . $switch, $firstExpression)) {
          $results[] = [
            'label' => '/' . $switch,
            'autocomplete' => '/' . $switch,
          ];
        }
      }
      array_shift($expressions);
    }

    foreach ($this->main->apps->getEnabledApps() as $appNamespace => $app) {
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
