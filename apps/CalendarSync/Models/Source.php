<?php

namespace HubletoApp\Community\CalendarSync\Models;

use ADIOS\Core\Db\Column\Boolean;
use ADIOS\Core\Db\Column\Color;
use ADIOS\Core\Db\Column\Varchar;

class Source extends \HubletoMain\Core\Models\Model
{
  public string $table = 'calendar_sync_sources';
  public string $recordManagerClass = RecordManagers\Source::class;

  public function describeColumns(): array
  {
    return array_merge(parent::describeColumns(), [
      'name' => (new Varchar($this, $this->translate('Name')))->setRequired(),
      'link' => (new Varchar($this, $this->translate('Calendar ID')))->setRequired(),
      'type' => (new Varchar($this, $this->translate('Type')))->setRequired()->setEnumValues(['google' => 'Google Calendar', 'ics' => '.ics Url']),
      'color' => (new Color($this, $this->translate('Color')))->setRequired(),
      'active' => (new Boolean($this, $this->translate('Active')))->setDefaultValue(true),
    ]);
  }

  public function describeTable(array $description = []): \ADIOS\Core\Description\Table
  {
    $description["model"] = $this->fullName;
    $description = parent::describeTable($description);
    $description->ui['title'] = 'Calendar sources';
    $description->ui['addButtonText'] = 'Add calendar source';
    $description->ui['showHeader'] = true;
    $description->ui['showFulltextSearch'] = true;
    $description->ui['showFooter'] = false;
    return $description;
  }

}
