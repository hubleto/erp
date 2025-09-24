<?php declare(strict_types=1);

namespace Hubleto\Erp\Api;

use Exception;

class GetTableColumnsCustomize extends \Hubleto\Erp\Controllers\ApiController
{
  public function renderJson(): array
  {
    try {
      $model = $this->getModel($this->router()->urlParamAsString("model"));
      $allColumnsConfig = @json_decode($model->configAsString('tableColumns'), true);
      $columns = $model->getColumns();
      $columnsConfig = $allColumnsConfig[$this->router()->urlParamAsString("tag")] ?? [];
      $transformedColumns = [];

      // TODO: There needs to be developed a way to get ALL the virtual columns
      // either by inicializing them beforehand in describeTable() of the model or somehow else
      // $descriptionColumns = $model->describeTable();
      // $descriptionColumns = $methodColumns->columns;
      // foreach ($methodColumns as $colName => $column) {
      //   var_dump(!is_array($column) ? $column->getTitle() : $column["title"]);
      // }
      // exit;

      if (!empty($columnsConfig)) {
        foreach ($columnsConfig as $colName => $is_hidden) {
          $originalColName = $columns[$colName]->getTitle();
          $transformedColumns[$colName]["title"] = $originalColName;
          $transformedColumns[$colName]["is_hidden"] = $is_hidden;
        }
      } else {
        foreach ($columns as $colName => $column) {
          $transformedColumns[$colName]["title"] = $column->getTitle();
          $transformedColumns[$colName]["is_hidden"] = $columnsConfig ? 0 : (int) !$column->getProperty("defaultVisibility");
        }
      }

      unset($transformedColumns["id"]);
    } catch (Exception $e) {
      return [
        "status" => "failed",
        "error" => $e
      ];
    }

    return [
      "data" => $transformedColumns,
      "status" => "success"
    ];
  }
}
