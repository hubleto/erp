<?php declare(strict_types=1);

namespace Hubleto\App\Community\Deals\Controllers\Api;

use Hubleto\App\Community\Deals\Models\Deal;

class GenerateQuotationPdf extends \Hubleto\Erp\Controllers\ApiController
{
  public function renderJson(): ?array
  {
    $idDeal = $this->router()->urlParamAsInteger('idDeal');

    /** @var Deal */
    $mDeal = $this->getModel(Deal::class);

    $idDocument = $mDeal->generateQuotationPdf($idDeal);

    return [
      'idDocument' => $idDocument
    ];
  }
}
