<?php

namespace HubletoApp\Community\Settings\Models;

use ADIOS\Core\Db\Column\Boolean;
use ADIOS\Core\Db\Column\Color;
use ADIOS\Core\Db\Column\Varchar;

class ActivityType extends \HubletoMain\Core\Models\Model
{
  public string $table = 'activity_types';
  public string $recordManagerClass = RecordManagers\ActivityType::class;
  public ?string $lookupSqlValue = '{%TABLE%}.name';

  public function describeColumns(): array
  {
    return array_merge(parent::describeColumns(), [
      'name' => (new Varchar($this, $this->translate('Type'))),
      'color' => (new Color($this, $this->translate('Color'))),
      'calendar_visibility' => (new Boolean($this, $this->translate('Show in calendar'))),
      'icon' => (new Varchar($this, $this->translate('Icon'))),
    ]);
  }

  public function describeTable(): \ADIOS\Core\Description\Table
  {
    $description = parent::describeTable();

    $description->ui['title'] = 'Activity Types';
    $description->ui['addButtonText'] = 'Add Activity Type';
    $description->ui['showHeader'] = true;
    $description->ui['showFulltextSearch'] = true;
    $description->ui['showFooter'] = false;

    return $description;
  }

}
