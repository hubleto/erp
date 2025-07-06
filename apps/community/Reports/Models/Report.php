<?php

namespace HubletoApp\Community\Projects\Models;

use \ADIOS\Core\Db\Column\Text;
use \ADIOS\Core\Db\Column\Varchar;

use \HubletoApp\Community\Settings\Models\User;

class Report extends \HubletoMain\Core\Models\Model
{

  public string $table = 'projects';
  public string $recordManagerClass = RecordManagers\Project::class;
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

}
