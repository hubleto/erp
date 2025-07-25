<?php

namespace HubletoApp\Community\Reports\Controllers\Api;

class GetConfig extends \HubletoMain\Controllers\ApiController
{
  public function renderJson(): ?array
  {
    $model = $this->main->urlParamAsString("model");
    $modelObj = $this->main->di->create($model::class);

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
