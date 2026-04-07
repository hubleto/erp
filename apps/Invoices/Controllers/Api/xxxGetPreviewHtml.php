<?php declare(strict_types=1);

namespace Hubleto\App\Community\Invoices\Controllers\Api;

use Hubleto\App\Community\Invoices\Models\Invoice;

class GetPreviewHtml extends \Hubleto\Erp\Controllers\ApiController
{
  public function renderJson(): array
  {
    $idInvoice = $this->router()->urlParamAsInteger('idInvoice');
    $idTemplate = $this->router()->urlParamAsInteger('idTemplate');

    $html = '';

    if ($idTemplate > 0) {

      /** @var Invoice */
      $mInvoice = $this->getModel(Invoice::class);

      $mInvoice->record->find($idInvoice)->update(['id_template' => $idTemplate]);

      try {
        $html = $mInvoice->getPreviewHtml($idInvoice);
      } catch (\Throwable $e) {
        $html = '<div class="alert alert-danger">Error generating preview: ' . $e->getMessage() . '</div>';
      }
    }

    return [
      'html' => $html,
    ];
  }
}
