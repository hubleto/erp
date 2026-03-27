<?php declare(strict_types=1);

namespace Hubleto\App\Community\Orders\Controllers\Api;

use Hubleto\App\Community\Orders\Models\Order;

class GetPreviewHtml extends \Hubleto\Erp\Controllers\ApiController
{
  public function renderJson(): array
  {
    $idOrder = $this->router()->urlParamAsInteger('idOrder');
    $idTemplate = $this->router()->urlParamAsInteger('idTemplate');

    $html = '';

    if ($idTemplate > 0) {

      /** @var Order */
      $mOrder = $this->getModel(Order::class);

      $mOrder->record->find($idOrder)->update(['id_template' => $idTemplate]);

      try {
        $html = $mOrder->getPreviewHtml($idOrder);
      } catch (\Throwable $e) {
        $html = '<div class="alert alert-danger">Error generating preview: ' . $e->getMessage() . '</div>';
      }
    }

    return [
      'html' => $html,
    ];
  }
}
