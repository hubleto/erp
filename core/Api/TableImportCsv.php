<?php

namespace HubletoMain\Core\Api;

use Exception;

class TableImportCsv extends \HubletoMain\Core\Controllers\ApiController
{
  public function renderJson(): ?array
  {
    $csvData = $this->main->urlParamAsString('csvData');
    return [
      "status" => "success",
      "csvDataLength" => strlen($csvData),
    ];
  }
}
