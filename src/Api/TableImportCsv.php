<?php declare(strict_types=1);

namespace Hubleto\Erp\Api;

use Exception;

class TableImportCsv extends \Hubleto\Erp\Controllers\ApiController
{
  public function renderJson(): ?array
  {
    $csvData = $this->getRouter()->urlParamAsString('csvData');
    return [
      "status" => "success",
      "csvDataLength" => strlen($csvData),
    ];
  }
}
