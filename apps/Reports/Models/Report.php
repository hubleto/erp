<?php

namespace HubletoApp\Community\Reports\Models;

use Hubleto\Framework\Db\Column\Text;
use Hubleto\Framework\Db\Column\Varchar;
use HubletoApp\Community\Settings\Models\User;

class Report extends \Hubleto\Framework\Models\Model
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

  public function describeTable(): \Hubleto\Framework\Description\Table
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
        $modelObj = $this->main->di->create($model::class);

        foreach ($modelObj->getColumns() as $colName => $column) {
          $field = [
            'name' => $colName,
            'label' => $column->getTitle(),
          ];

          if (
            $column instanceof \Hubleto\Framework\Db\Column\Decimal
            || $column instanceof \Hubleto\Framework\Db\Column\Integer
          ) {
            $field['inputType'] = 'number';
          }

          if (
            $column instanceof \Hubleto\Framework\Db\Column\Boolean
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
