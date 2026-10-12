<?php

namespace Hubleto\App\Community\PriceList\Controllers\Api;

use Hubleto\App\Community\PriceList\Services\PriceResolver;
use Hubleto\App\Community\PriceList\Services\Settings;

class ResolvePrices extends \Hubleto\Erp\Controllers\ApiController
{
  public function renderJson(): array
  {
    /** @var PriceResolver $resolver */
    $resolver = $this->getService(PriceResolver::class);
    /** @var Settings $settings */
    $settings = $this->getService(Settings::class);

    $queries = $this->router()->urlParamAsArray('queries');
    $max = $settings->getInt('maxBatchItems');

    if ($max > 0 && count($queries) > $max) {
      return ['status' => 'error', 'message' => $this->translate('Too many items in one request.')];
    }

    return [
      'status' => 'success',
      'results' => $resolver->resolveMany($queries),
    ];
  }
}
