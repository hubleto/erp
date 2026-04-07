<?php declare(strict_types=1);

namespace Hubleto\App\Community\Documents\Controllers\Api;

use Hubleto\App\Community\Documents\Generator;

class GetPreviewVars extends \Hubleto\Erp\Controllers\ApiController
{
  public function renderJson(): array
  {
    $model = $this->router()->urlParamAsString('model');
    $recordId = $this->router()->urlParamAsInteger('recordId');

    /** @var Generator */
    $generator = $this->getService(Generator::class);

    return [
      'vars' => $generator->getPreviewVars($model, $recordId)
    ];
  }
}
