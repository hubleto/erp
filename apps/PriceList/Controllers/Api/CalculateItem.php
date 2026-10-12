<?php

namespace Hubleto\App\Community\PriceList\Controllers\Api;

use Hubleto\App\Community\PriceList\Services\PriceCalculator;

class CalculateItem extends \Hubleto\Erp\Controllers\ApiController
{
  public function renderJson(): array
  {
    /** @var PriceCalculator $calc */
    $calc = $this->getService(PriceCalculator::class);
    $router = $this->router();

    $item = [
      'unit_price' => $router->urlParamAsFloat('unit_price'),
      'amount' => $router->urlParamAsFloat('amount', 1),
      'discount' => $router->urlParamAsFloat('discount'),
      'vat' => $router->urlParamAsFloat('vat'),
      'price_includes_vat' => $router->urlParamAsBool('price_includes_vat'),
    ];

    $options = [];
    if ($router->urlParamNotEmpty('vat_basis')) $options['vat_basis'] = $router->urlParamAsString('vat_basis');
    if ($router->urlParamNotEmpty('rounding')) $options['rounding'] = $router->urlParamAsString('rounding');

    return ['status' => 'success', 'item' => $calc->calculateItem($item, $options)];
  }
}
