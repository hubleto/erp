<?php declare(strict_types=1);

namespace HubletoMain\Api;

use Exception;

class Search extends \HubletoMain\Controllers\ApiController
{
  public function renderJson(): ?array
  {
    $query = $this->main->urlParamAsString('query');

    $expressions = [];
    foreach (explode(' ', strtr($query, ',', ' ')) as $e) {
      $expressions[] = strtolower(trim($e, ' ,;./'));
    }

    $results = [];

    foreach ($this->main->apps->getEnabledApps() as $appNamespace => $app) {
      $appResults = $app->search($expressions);
      foreach ($appResults as $key => $value) {
        $value['APP_NAMESPACE'] = $appNamespace;
        $results[] = $value;
      }
    }

    return $results;
  }
}
