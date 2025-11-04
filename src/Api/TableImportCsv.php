<?php declare(strict_types=1);

namespace Hubleto\Erp\Api;

use Exception;

class TableImportCsv extends \Hubleto\Erp\Controllers\ApiController
{
  public function renderJson(): array
  {
    try {
      $modelName = $this->router()->urlParamAsString('model');
      $csvData = $this->router()->urlParamAsString('csvData');
      $defaultCsvImportValues = $this->router()->urlParamAsArray('defaultCsvImportValues');
      $testImport = $this->router()->urlParamAsBool('testImport');

      if (str_starts_with($csvData, 'data:text/csv;base64,')) {
        $csvData = base64_decode(str_replace('data:text/csv;base64,', '', $csvData));
      }

      $model = $this->getModel($modelName);

      $foundRecords = [];
      $columnsMap = [];
      $rowNr = 0;
      $importedRecords = 0;

      foreach (explode("\n", $csvData) as $csvLine) {
        if (empty(trim($csvLine))) continue;

        if ($rowNr == 0) {
          $columnsMap = str_getcsv(trim($csvLine));
        } else {
          $row = str_getcsv(trim(iconv("Windows-1250", "UTF-8//TRANSLIT//IGNORE", $csvLine)));

          $record = $defaultCsvImportValues;
          foreach ($row as $colNr => $colValue) {
            $record[$columnsMap[$colNr]] = $colValue;
          }

          if ($testImport) {
            $foundRecords[] = $record;
          } else {
            $model->record->recordCreate($record);
            $importedRecords++;
          }
        }
        $rowNr++;
      }

      $result = [
        "status" => "success",
      ];

      if ($testImport) {
        $result['foundRecords'] = $foundRecords;
      } else {
        $result['importedRecords'] = $importedRecords;
      }

      return $result;
    } catch (\Throwable $e) {
      return ["status" => "error", "message" => $e->getMessage()];
    }
  }
}
