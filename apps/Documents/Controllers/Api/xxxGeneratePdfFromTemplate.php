<?php declare(strict_types=1);

namespace Hubleto\App\Community\Documents\Controllers\Api;

use Hubleto\App\Community\Documents\Generator;
use Hubleto\App\Community\Documents\Models\Template;

class xxxGeneratePdfFromTemplate extends \Hubleto\Erp\Controllers\ApiController
{
  public function renderJson(): array
  {
    $idTemplate = $this->router()->urlParamAsInteger('idTemplate');
    $outpuFilename = $this->router()->urlParamAsString('outpuFilename');
    $vars = $this->router()->urlParamAsArray('vars');

    /** @var Template */
    $mTemplate = $this->getService(Template::class);
    $template = $mTemplate->record->prepareReadQuery()->where('id', $idTemplate)->get();

    /** @var Generator */
    $generator = $this->getService(Generator::class);
    $idDocument = $generator->createPdfDocumentFromTemplate(
      '',
      0,
      $template->id,
      $outpuFilename,
      $vars
    );

    return $idDocument;
  }
}
