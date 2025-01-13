<?php

namespace HubletoMain\Core;

class Model extends \ADIOS\Core\Model {
  public \HubletoMain $main;

  public array $conversionMap = [];
  public array $conversionRelations = [];

  function __construct(\ADIOS\Core\Loader $main)
  {
    $this->main = $main;
    parent::__construct($main);
  }

  public function tableDescribe(array $description = []): array {
    $description = parent::tableDescribe($description);

    $origColumns = $this->columns();
    unset($origColumns['id']);

    $columns = [];
    foreach ($origColumns as $colName => $colDef) {
      $columns[$this->conversionMap[$colName] ?? $colName] = $colDef;
    }

    $description['columns'] = $columns;

    return $description;
  }

  public function recordSave(array $data) {
    $data = $this->convertRecord($data, true);
    return parent::recordSave($data);
  }

  public function onAfterLoadRecord(array $data): array {
    return $this->convertRecord($data);
  }

  public function convertRecord(array $row, bool $reverseConversion = false): array {
    $conversionMap = ($reverseConversion ? array_flip($this->conversionMap) : $this->conversionMap);

    $newRow = [];
    foreach ($row as $colName => $colValue) {
      $newRow[$conversionMap[$colName] ?? $colName] = $colValue;
    }

    foreach ($this->conversionRelations as $relation => $tmp) {
      list($relationType, $modelClass) = $tmp;
      switch ($relationType) {
        case 'belongsTo':
          if (is_array($row[$relation])) {
            $newRow[$relation] = (new $modelClass($this->main))->convertRecord($row[$relation], $reverseConversion);
          }
        break;
        case 'hasMany':
          if (is_array($row[$relation])) {
            $newRow[$relation] = (new $modelClass($this->main))->convert($row[$relation], $reverseConversion);
          }
        break;
      }
    }

    return $newRow;
  }

  public function convert(array $rows, bool $reverseConversion = false): array {
    foreach ($rows as $rowKey => $row) {
      $rows[$rowKey] = $this->convertRecord($row, $reverseConversion);
    }
    return $rows;
  }
}