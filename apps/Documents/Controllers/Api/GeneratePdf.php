<?php declare(strict_types=1);

namespace Hubleto\App\Community\Documents\Controllers\Api;

use Hubleto\App\Community\Documents\Generator;

class GeneratePdf extends \Hubleto\Erp\Controllers\ApiController
{
  public function renderJson(): array
  {

    $model = $this->router()->urlParamAsString('model');
    $recordId = $this->router()->urlParamAsInteger('recordId');
    $documentName = $this->router()->urlParamAsString('documentName');

    /** @var Generator */
    $generator = $this->getService(Generator::class);

    list($idDocument, $pdfFile) = $generator->generatePdf($model, $recordId, $documentName);

    return [
      'idDocument' => $idDocument,
      'pdfFile' => $pdfFile,
    ];
  }
}
