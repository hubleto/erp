<?php

namespace Hubleto\App\Community\PriceList\Controllers\Api;

use Hubleto\App\Community\PriceList\Services\PriceCalculator;
use Hubleto\App\Community\PriceList\Services\Settings;

class CalculateItems extends \Hubleto\Erp\Controllers\ApiController
{
  public function renderJson(): array
  {
    /** @var PriceCalculator $calc */
    $calc = $this->getService(PriceCalculator::class);
    /** @var Settings $settings */
    $settings = $this->getService(Settings::class);
    $router = $this->router();

    $items = $router->urlParamAsArray('items');
    $max = $settings->getInt('maxBatchItems');
    if ($max > 0 && count($items) > $max) {
      return ['status' => 'error', 'message' => $this->translate('Too many items in one request.')];
    }

    $options = [];
    if ($router->urlParamNotEmpty('vat_mode')) $options['vat_mode'] = $router->urlParamAsString('vat_mode');
    if ($router->urlParamNotEmpty('rounding')) $options['rounding'] = $router->urlParamAsString('rounding');

    $result = $calc->calculateItems($items, $options);

    return ['status' => 'success', 'items' => $result['items'], 'totals' => $result['totals']];
  }
}
