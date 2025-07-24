<?php

namespace Hubleto\Framework\Api;

use Exception;

class TableImportCsv extends \Hubleto\Framework\Controllers\ApiController
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
