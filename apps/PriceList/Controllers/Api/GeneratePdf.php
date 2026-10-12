<?php

namespace Hubleto\App\Community\PriceList\Controllers\Api;

use Hubleto\App\Community\Documents\Generator;
use Hubleto\App\Community\PriceList\Models\PriceList;
use Hubleto\App\Community\PriceList\Models\PriceListHasDocument;

/**
 * Generates a PDF of a price list with Documents\Generator and links the document to the price list.
 */
class GeneratePdf extends \Hubleto\Erp\Controllers\ApiController
{
  public function renderJson(): array
  {
    $idPriceList = $this->router()->urlParamAsInteger('idPriceList');

    /** @var PriceList $mPriceList */
    $mPriceList = $this->getModel(PriceList::class);
    $priceList = $mPriceList->record->where($mPriceList->table . '.id', $idPriceList)->first();
    if (!$priceList) return ['status' => 'error', 'message' => $this->translate('Price list was not found.')];

    try {
      $result = $this->generate($priceList);
    } catch (\Throwable $e) {
      return ['status' => 'error', 'message' => $e->getMessage()];
    }

    return ['status' => 'success', 'idDocument' => $result['idDocument'], 'file' => $result['file']];
  }

  /**
   * Generates the PDF and links it to the price list.
   *
   * @param mixed $priceList Record of the price list
   *
   * @return array ['idDocument', 'file']
   *
   */
  public function generate(mixed $priceList): array
  {
    /** @var Generator $generator */
    $generator = $this->getService(Generator::class);

    $documentName = $priceList->code . ' ' . $priceList->name;
    $slug = trim((string) preg_replace('/[^a-z0-9]+/', '-', strtolower($documentName)), '-');
    $file = ($slug === '' ? 'price-list' : $slug) . '-' . date('Ymd-His') . '.pdf';

    $vars = $generator->getPreviewVars(PriceList::class, (int) $priceList->id);
    $idDocument = $generator->generatePdfDocumentFromTemplate($documentName, PriceList::class, (int) $priceList->id, 0, $file, $vars, true);

    if ($idDocument > 0) {
      /** @var PriceListHasDocument $mLink */
      $mLink = $this->getModel(PriceListHasDocument::class);
      $exists = $mLink->record
        ->where($mLink->table . '.id_price_list', (int) $priceList->id)
        ->where($mLink->table . '.id_document', $idDocument)
        ->count() > 0
      ;
      if (!$exists) $mLink->record->recordCreate(['id_price_list' => (int) $priceList->id, 'id_document' => $idDocument]);
    }

    return ['idDocument' => $idDocument, 'file' => $file];
  }
}
