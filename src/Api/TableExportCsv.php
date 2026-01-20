<?php declare(strict_types=1);

namespace Hubleto\Erp\Api;

use Exception;

class TableExportCsv extends \Hubleto\Erp\Controller
{
  public \Hubleto\Framework\Model $model;

  public function render(): string
  {
    $model = $this->router()->urlParamAsString('model');
    $this->model = $this->getModel($model);

    $records = $this->model->record->loadTableData(
      $this->router()->urlParamAsString('fulltextSearch'),
      $this->router()->urlParamAsArray('columnSearch'),
      $this->router()->urlParamAsArray('orderBy'),
      99999999, // itemsPerPage
      0, // page
      '', // dataView
    );

    $separator = $this->router()->urlParamAsString('separator', ',');
    $data = $records['data'] ?? [];

    $csvContent = "";
    $columns = $this->model->getColumns();

    $cols = [];
    foreach ($columns as $columnName => $column) {
      $cols[] = $column->getTitle();
    }
    $csvContent .= join($separator, $cols) . "\n";

    foreach ($data as $row) {
      $cols = [];
      foreach ($columns as $columnName => $column) {
        $valueStr = '';

        if ($column instanceof \Hubleto\Framework\Db\Column\Lookup) {
          $value = $row['_LOOKUP[' . $columnName . ']'] ?? '';
        } else {
          $value = $row[$columnName] ?? '';
        }

        if (is_array($value)) {
          $valueStr = (string) json_encode($value);
        } elseif (is_object($value)) {
          $valueStr = (string) json_encode($value);
        } else {
          $valueStr = (string) $value;
        }

        $cols[] = '"' . str_replace('"', '\\"', $valueStr) . '"';
      }
      $csvContent .= join($separator, $cols) . "\n";
    }

    header('Content-Type: application/csv');
    header('Content-Disposition: attachment; filename=Export-' . $this->model->shortName . '- ' . date('Ymd-His') . '.csv');
    header('Pragma: no-cache');

    return iconv("UTF-8", "windows-1250//TRANSLIT", $csvContent);
  }
}
