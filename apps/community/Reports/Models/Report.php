<?php

namespace HubletoApp\Community\Reports\Models;

use \ADIOS\Core\Db\Column\Text;
use \ADIOS\Core\Db\Column\Varchar;

use \HubletoApp\Community\Settings\Models\User;

class Report extends \HubletoMain\Core\Models\Model
{

  public string $table = 'reports';
  public string $recordManagerClass = RecordManagers\Report::class;
  public ?string $lookupSqlValue = 'concat("Report #", {%TABLE%}.id)';

  public function describeColumns(): array
  {
    return array_merge(parent::describeColumns(), [
      'title' => (new Varchar($this, $this->translate('Title')))->setProperty('defaultVisibility', true),
      'model' => (new Varchar($this, $this->translate('Model')))->setProperty('defaultVisibility', true),
      'query' => (new Text($this, $this->translate('Query'))),
      'notes' => (new Varchar($this, $this->translate('Notes'))),
    ]);
  }

  public function describeTable(): \ADIOS\Core\Description\Table
  {
    $description = parent::describeTable();
    $description->ui['addButtonText'] = 'Add Report';
    $description->ui['showHeader'] = true;
    $description->ui['showFulltextSearch'] = true;
    $description->ui['showFooter'] = false;
    return $description;
  }

  public function onAfterLoadRecord(array $record): array
  {

    try {
      $model = $record['model'];
      if (class_exists($model)) {
        $modelObj = new $model($this->main);

        foreach ($modelObj->getColumns() as $colName => $column) {
          $field = [
            'name' => $colName,
            'label' => $column->getTitle(),
          ];

          if (
            $column instanceof \ADIOS\Core\Db\Column\Decimal
            || $column instanceof \ADIOS\Core\Db\Column\Integer
          ) {
            $field['inputType'] = 'number';
          }

          if (
            $column instanceof \ADIOS\Core\Db\Column\Boolean
          ) {
            $field['valueEditorType'] = 'checkbox';
          }

          $fields[] = $field;
        }

      }
    } catch (Exception $e) {
      $fields = [];
    }

    $record['_QUERY_BUILDER'] = [
      'fields' => $fields,
    ];

    return $record;
  }

}
