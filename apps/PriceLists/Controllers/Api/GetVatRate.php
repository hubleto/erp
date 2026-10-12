<?php

namespace Hubleto\App\Community\PriceLists\Controllers\Api;

use Hubleto\App\Community\PriceLists\Services\VatCalculator;

class GetVatRate extends \Hubleto\Erp\Controllers\ApiController
{
  public function renderJson(): array
  {
    /** @var VatCalculator $vatCalculator */
    $vatCalculator = $this->getService(VatCalculator::class);
    $router = $this->router();

    $query = [
      'idProduct' => $router->urlParamAsInteger('idProduct'),
      'idCountry' => $router->urlParamAsInteger('idCountry'),
      'idCustomer' => $router->urlParamAsInteger('idCustomer'),
      'date' => $router->urlParamAsString('date', date('Y-m-d')),
      'type' => $router->urlParamAsInteger('type'),
    ];
    if ($router->urlParamAsString('vat', '') !== '') $query['vat'] = $router->urlParamAsFloat('vat');

    try {
      return ['status' => 'success', 'rate' => $vatCalculator->resolveRate($query)];
    } catch (\Throwable $e) {
      return ['status' => 'error', 'message' => $e->getMessage()];
    }
  }
}
