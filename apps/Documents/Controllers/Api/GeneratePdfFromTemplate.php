<?php declare(strict_types=1);

namespace Hubleto\App\Community\Documents\Controllers\Api;

use Hubleto\App\Community\Documents\Generator;
use Hubleto\App\Community\Documents\Models\Template;

class GeneratePdfFromTemplate extends \Hubleto\Erp\Controllers\ApiController
{
  public function renderJson(): ?array
  {
    $idTemplate = $this->router()->urlParamAsInteger('idTemplate');
    $outpuFilename = $this->router()->urlParamAsString('outpuFilename');
    $vars = $this->router()->urlParamAsArray('vars');

    $mTemplate = $this->getService(Template::class);
    $template = $mTemplate->record->prepareReadQuery()->where('id', $idTemplate)->get();

    $generator = $this->getService(Generator::class);
    $idDocument = $generator->generatePdfFromTemplate(
      $template->id,
      $outpuFilename,
      $vars
    );

    return $idDocument;
  }
}
