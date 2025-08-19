<?php declare(strict_types=1);

namespace HubletoMain\Api;

use Exception;

class Search extends \HubletoMain\Controllers\ApiController
{
  public function renderJson(): ?array
  {
    $query = $this->main->urlParamAsString('query');

    $expressions = [];
    foreach (explode(' ', strtr($query, ',;.', '   ')) as $e) {
      $expressions[] = trim($e);
    }

    $results = [];

    foreach ($this->main->apps->getEnabledApps() as $appNamespace => $app) {
      $canSearchThisApp = true;
      $expressionsToSearch = $expressions;
      foreach ($expressions as $key => $e) {
        if (str_starts_with($e, 'app:')) {
          unset($expressionsToSearch[$key]);
          if (!str_contains(strtolower($app->fullName), strtolower(str_replace('app:', '', $e)))) {
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

      if ($canSearchThisApp) {
        $appResults = $app->search($expressionsToSearch);
        foreach ($appResults as $key => $value) {
          $value['APP_NAMESPACE'] = $appNamespace;
          $results[] = $value;
        }
      }
    }

    // $results[] = [
    //   'label' => 'Help: How to use search',
    //   'url' => 'help/search',
    // ];

    return $results;
  }
}
