<?php declare(strict_types=1);

namespace Hubleto\App\Community\Documents\Controllers\Api;

use Hubleto\App\Community\Documents\Generator;

class GetPreviewHtml extends \Hubleto\Erp\Controllers\ApiController
{
  public function renderJson(): array
  {
    $model = $this->router()->urlParamAsString('model');
    $recordId = $this->router()->urlParamAsInteger('recordId');
    $idTemplate = $this->router()->urlParamAsInteger('idTemplate');

    $html = '';

    if (!empty($model)) {
      try {
        /** @var Generator */
        $generator = $this->getService(Generator::class);

        /** @var Hubleto\ErpModel */
        $mObj = $this->getService($model);

        if ($idTemplate > 0) {
          $mObj->record->where($mObj->table . '.id', $recordId)->update(['id_template' => $idTemplate]);
        }

        $record = $mObj->record->prepareReadQuery()->where($mObj->table . '.id', $recordId)->first();
        if (!$record) throw new \Exception('Record to generate PDF from was not found.');

        $html = $generator->getPreviewHtml($model, $recordId, $idTemplate);
      } catch (\Throwable $e) {
        $html = '<div class="alert alert-danger">Error generating preview: ' . $e->getMessage() . '</div>';
      }
    }

    return [
      'html' => $html,
    ];
  }
}
