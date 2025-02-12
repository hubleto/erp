<?php

namespace HubletoApp\Community\CalendarSync\Models;

use ADIOS\Core\Db\Column\Boolean;
use ADIOS\Core\Db\Column\Color;
use ADIOS\Core\Db\Column\Varchar;

class Source extends \HubletoMain\Core\Model
{
  public string $table = 'sources';
  public string $eloquentClass = \HubletoApp\Community\CalendarSync\Models\Eloquent\Source::class;

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

  public function tableDescribe(array $description = []): array
  {
    $description["model"] = $this->fullName;
    $description = parent::tableDescribe($description);
    $description['ui']['title'] = 'Calendar sources';
    $description['ui']['addButtonText'] = 'Add calendar source';
    $description['ui']['showHeader'] = true;
    $description['ui']['showFooter'] = false;
    return $description;
  }
}
