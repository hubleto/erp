<?php declare(strict_types=1);

namespace HubletoMain\Api;

use Exception;

class TableImportCsv extends \HubletoMain\Controllers\ApiController
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
