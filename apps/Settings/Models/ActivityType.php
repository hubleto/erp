<?php

namespace HubletoApp\Community\Settings\Models;

use Hubleto\Framework\Db\Column\Boolean;
use Hubleto\Framework\Db\Column\Color;
use Hubleto\Framework\Db\Column\Varchar;

class ActivityType extends \HubletoMain\Model
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

  public function describeTable(): \Hubleto\Framework\Description\Table
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
