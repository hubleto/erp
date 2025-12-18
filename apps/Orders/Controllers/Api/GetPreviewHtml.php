<?php declare(strict_types=1);

namespace Hubleto\App\Community\Orders\Controllers\Api;

use Hubleto\App\Community\Orders\Models\Order;

class GetPreviewHtml extends \Hubleto\Erp\Controllers\ApiController
{
  public function renderJson(): array
  {
    $idOrder = $this->router()->urlParamAsInteger('idOrder');
    $idTemplate = $this->router()->urlParamAsInteger('idTemplate');

    /** @var Order */
    $mOrder = $this->getModel(Order::class);

    $mOrder->record->find($idOrder)->update(['id_template' => $idTemplate]);

    return [
      'html' => $mOrder->getPreviewHtml($idOrder)
    ];
  }
}
