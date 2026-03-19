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
      $failedImports = [];
      $rowNr = 0;
      $importedRecords = 0;
      $separator = ",";

      foreach (explode("\n", $csvData) as $csvLine) {
        if (empty(trim($csvLine))) continue;

        if ($rowNr == 0) {
          $columnsMap = str_getcsv(trim($csvLine));
          if (!is_array($columnsMap) || (is_array($columnsMap) && count($columnsMap) == 1)) {
            $separator = ";";
            $columnsMap = str_getcsv(trim($csvLine), $separator);
          }
        } else {
          $row = str_getcsv(trim($csvLine), $separator);

          $record = $defaultCsvImportValues;
          foreach ($row as $colNr => $colValue) {
            $record[$columnsMap[$colNr]] = $colValue;
          }

          if ($testImport) {
            $foundRecords[] = $record;
          } else {
            try {
              $model->record->recordCreate($record);
              $importedRecords++;
            } catch (\Throwable $e) {
              $failedImports[] = $e->getMessage();
            }
          }
        }
        $rowNr++;
      }

      $result = [
        "status" => "success",
        "failedImports" => $failedImports,
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
