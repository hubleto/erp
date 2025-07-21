<?php

namespace HubletoMain\Core\Api;

use Exception;

class GetTableColumnsCustomize extends \HubletoMain\Core\Controllers\ApiController
{
  public function renderJson(): ?array
  {
    try {
      $model = $this->main->getModel($this->main->urlParamAsString("model"));
      $allColumnsConfig = @json_decode($model->getConfigAsString('tableColumns'), true);
      $columns = $model->getColumns();
      $columnsConfig = $allColumnsConfig[$this->main->urlParamAsString("tag")] ?? [];
      $transformedColumns = [];

      // TODO: There needs to be developed a way to get ALL the virtual columns
      // either by inicializing them beforehand in describeTable() of the model or somehow else
      // $descriptionColumns = $model->describeTable();
      // $descriptionColumns = $methodColumns->columns;
      // foreach ($methodColumns as $colName => $column) {
      //   var_dump(!is_array($column) ? $column->getTitle() : $column["title"]);
      // }
      // exit;

      foreach ($columns as $colName => $column) {
        $transformedColumns[$colName]["title"] = $column->getTitle();
        $transformedColumns[$colName]["is_hidden"] = $columnsConfig ? 0 : (int) !$column->getProperty("defaultVisibility");
      }

      foreach ($columnsConfig as $key => $is_hidden) {
        $transformedColumns[$key]["is_hidden"] = $is_hidden;
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
