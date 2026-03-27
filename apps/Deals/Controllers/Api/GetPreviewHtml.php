<?php declare(strict_types=1);

namespace Hubleto\App\Community\Deals\Controllers\Api;

use Hubleto\App\Community\Deals\Models\Deal;

class GetPreviewHtml extends \Hubleto\Erp\Controllers\ApiController
{
  public function renderJson(): array
  {
    $idDeal = $this->router()->urlParamAsInteger('idDeal');
    $idTemplate = $this->router()->urlParamAsInteger('idTemplate');

    $html = '';

    if ($idTemplate > 0) {

      /** @var Deal */
      $mDeal = $this->getModel(Deal::class);

      $mDeal->record->find($idDeal)->update(['id_template' => $idTemplate]);

      try {
        $html = $mDeal->getPreviewHtml($idDeal);
      } catch (\Throwable $e) {
        $html = '<div class="alert alert-danger">Error generating preview: ' . $e->getMessage() . '</div>';
      }
    }

    return [
      'html' => $html,
    ];
  }
}
