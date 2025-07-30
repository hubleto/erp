<?php

namespace HubletoMain\Api;

use Exception;

class TableImportCsv extends \HubletoMain\Controllers\ApiController
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
