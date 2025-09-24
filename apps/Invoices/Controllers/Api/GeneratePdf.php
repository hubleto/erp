<?php declare(strict_types=1);

namespace Hubleto\App\Community\Invoices\Controllers\Api;

use Hubleto\App\Community\Invoices\Models\Invoice;

class GeneratePdf extends \Hubleto\Erp\Controllers\ApiController
{
  public function renderJson(): array
  {
    $idInvoice = $this->router()->urlParamAsInteger('idInvoice');

    /** @var Invoice */
    $mInvoice = $this->getModel(Invoice::class);

    $idDocument = $mInvoice->generatePdf($idInvoice);
    return [
      'idDocument' => $idDocument
    ];
  }
}
