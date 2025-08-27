<?php declare(strict_types=1);

namespace HubletoApp\Community\Orders\Controllers\Api;

use HubletoApp\Community\Orders\Models\Order;

class GenerateInvoice extends \HubletoMain\Controllers\ApiController
{
  public function renderJson(): ?array
  {
    $idOrder = $this->getRouter()->urlParamAsInteger('idOrder');

    $mOrder = $this->getService(Order::class);
    $idInvoice = $mOrder->generateInvoice($idOrder);

    return $idInvoice;
  }
}
