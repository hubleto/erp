<?php

namespace Hubleto\App\Community\Reports\Controllers\Api;

class GetConfig extends \Hubleto\Erp\Controllers\ApiController
{
  public function renderJson(): array
  {
    $model = $this->router()->urlParamAsString("model");
    $modelObj = $this->getModel($model::class);

    $fields = [];
    foreach ($modelObj->getColumns() as $colName => $column) {
      $fields[] = [
        'name' => $colName,
        'label' => $column->getTitle(),
      ];

      if (
        $column instanceof \Hubleto\Framework\Db\Column\Decimal
        || $column instanceof \Hubleto\Framework\Db\Column\Integer
      ) {
        $fields['inputType'] = 'number';
      }

      if (
        $column instanceof \Hubleto\Framework\Db\Column\Boolean
      ) {
        $fields['valueEditorType'] = 'checkbox';
      }
    }

    return [
      'fields' => $fields
    ];
  }
}
