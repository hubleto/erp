<?php

namespace Hubleto\App\Community\PriceList\Services;

use Hubleto\Erp\Core;

/**
 * Base of API controllers that take a price resolver query from URL/JSON parameters.
 */
class ApiQueryParser extends Core
{
  /** Reads the query array of PriceResolver::resolve() from request parameters. */
  public function readQuery(): array
  {
    $router = $this->router();

    $query = [
      'idProduct' => $router->urlParamAsInteger('idProduct'),
      'qty' => $router->urlParamAsFloat('qty', 1),
      'idUnit' => $router->urlParamAsInteger('idUnit'),
      'date' => $router->urlParamAsString('date', date('Y-m-d')),
      'type' => $router->urlParamAsString('type', 'sales'),
      'idCustomer' => $router->urlParamAsInteger('idCustomer'),
      'idSupplier' => $router->urlParamAsInteger('idSupplier'),
      'idShop' => $router->urlParamAsInteger('idShop'),
      'idCashRegister' => $router->urlParamAsInteger('idCashRegister'),
      'idDeal' => $router->urlParamAsInteger('idDeal'),
      'idProject' => $router->urlParamAsInteger('idProject'),
      'idActivityType' => $router->urlParamAsInteger('idActivityType'),
      'idCurrency' => $router->urlParamAsInteger('idCurrency'),
      'idPriceListHint' => $router->urlParamAsInteger('idPriceListHint'),
    ];

    if ($router->urlParamAsString('vat', '') !== '') $query['vat'] = $router->urlParamAsFloat('vat');

    return $query;
  }
}
