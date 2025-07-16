<?php

namespace HubletoApp\Community\Reports\Controllers\Api;

class GetConfig extends \HubletoMain\Core\Controllers\ApiController
{

  public function renderJson(): ?array
  {
    $model = $this->main->urlParamAsString("model");
    $modelObj = new $model($this->main);

    $fields = [];
    foreach ($modelObj->getColumns() as $colName => $column) {
      $fields[] = [
        'name' => $colName,
        'label' => $column->getTitle(),
      ];

      if (
        $column instanceof \ADIOS\Core\Db\Column\Decimal
        || $column instanceof \ADIOS\Core\Db\Column\Integer
      ) {
        $fields['inputType'] = 'number';
      }

      if (
        $column instanceof \ADIOS\Core\Db\Column\Boolean
      ) {
        $fields['valueEditorType'] = 'checkbox';
      }
    }

    return [
      'fields' => $fields
    ];
  }
}
