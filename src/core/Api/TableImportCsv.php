<?php

namespace HubletoMain\Core\Api;

use Exception;

class TableImportCsv extends \HubletoMain\Core\Controllers\Controller
{
  public int $returnType = \ADIOS\Core\Controller::RETURN_TYPE_JSON;
  public bool $permittedForAllUsers = true;

  public function renderJson(): ?array
  {
    $csvData = $this->main->urlParamAsString('csvData');
    return [
      "status" => "success",
      "csvDataLength" => strlen($csvData),
    ];
  }
}
