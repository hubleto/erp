<?php declare(strict_types=1);

namespace HubletoMain\Api;

use Exception;

class TableExportCsv extends \HubletoMain\Controller
{
  public \Hubleto\Framework\Model $model;

  public function render(array $params): string
  {
    $model = $this->main->urlParamAsString('model');
    $this->model = $this->main->getModel($model);

    $records = $this->model->recordGetList(
      $this->main->urlParamAsString('fulltextSearch'),
      $this->main->urlParamAsArray('columnSearch'),
      $this->main->urlParamAsArray('orderBy'),
      99999999, // itemsPerPage
      0, // page
    );

    $data = $records['data'] ?? [];

    $csvContent = "";
    $columns = $this->model->getColumns();

    $cols = [];
    foreach ($columns as $columnName => $column) {
      $cols[] = $column->getTitle();
    }
    $csvContent .= join(",", $cols) . "\n";

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
      $csvContent .= join(",", $cols) . "\n";
    }

    header('Content-Type: application/csv');
    header('Content-Disposition: attachment; filename=Export-' . $this->model->shortName . '- ' . date('Ymd-His') . '.csv');
    header('Pragma: no-cache');

    echo iconv("UTF-8", "windows-1250//TRANSLIT", $csvContent);

    exit;
  }
}
