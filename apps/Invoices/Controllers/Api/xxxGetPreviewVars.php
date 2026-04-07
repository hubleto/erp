<?php declare(strict_types=1);

namespace Hubleto\App\Community\Invoices\Controllers\Api;

use Hubleto\App\Community\Invoices\Models\Invoice;

class GetPreviewVars extends \Hubleto\Erp\Controllers\ApiController
{
  public function renderJson(): array
  {
    $idInvoice = $this->router()->urlParamAsInteger('idInvoice');
    $idTemplate = $this->router()->urlParamAsInteger('idTemplate');

    /** @var Invoice */
    $mInvoice = $this->getModel(Invoice::class);

    return [
      'vars' => $mInvoice->getPreviewVars($idInvoice)
    ];
  }
}
