<?php

namespace Hubleto\App\Community\PriceList\Controllers\Api;

use Hubleto\App\Community\PriceList\Services\PriceCalculator;

class ConvertVat extends \Hubleto\Erp\Controllers\ApiController
{
  public function renderJson(): array
  {
    /** @var PriceCalculator $calc */
    $calc = $this->getService(PriceCalculator::class);
    $router = $this->router();

    return [
      'status' => 'success',
      'price' => $calc->convertVatMode(
        $router->urlParamAsFloat('price'),
        $router->urlParamAsFloat('vat'),
        $router->urlParamAsBool('fromInclVat'),
        $router->urlParamAsBool('toInclVat')
      ),
    ];
  }
}
