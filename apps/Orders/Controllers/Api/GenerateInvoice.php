<?php declare(strict_types=1);

namespace Hubleto\App\Community\Orders\Controllers\Api;

use Hubleto\App\Community\Orders\Models\Order;

class GenerateInvoice extends \Hubleto\Erp\Controllers\ApiController
{
  public function renderJson(): array
  {
    $idOrder = $this->router()->urlParamAsInteger('idOrder');

    /** @var Order */
    $mOrder = $this->getModel(Order::class);

    $idInvoice = $mOrder->generateInvoice($idOrder);

    return ["status" => "success", "idInvoice" => $idInvoice];
  }
}
