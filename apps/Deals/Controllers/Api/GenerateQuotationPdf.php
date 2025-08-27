<?php declare(strict_types=1);

namespace Hubleto\App\Community\Deals\Controllers\Api;

use Hubleto\App\Community\Deals\Models\Deal;

class GenerateQuotationPdf extends \Hubleto\Erp\Controllers\ApiController
{
  public function renderJson(): ?array
  {
    $idDeal = $this->getRouter()->urlParamAsInteger('idDeal');
    $mDeal = $this->getService(Deal::class);
    $idDocument = $mDeal->generateQuotationPdf($idDeal);
    return [
      'idDocument' => $idDocument
    ];
  }
}
