<?php

namespace HubletoApp\Community\Campaigns\Models;

use HubletoApp\Community\Settings\Models\User;

use ADIOS\Core\Db\Column\Color;
use ADIOS\Core\Db\Column\Varchar;
use ADIOS\Core\Db\Column\Text;
use ADIOS\Core\Db\Column\Lookup;
use ADIOS\Core\Db\Column\DateTime;

class Campaign extends \HubletoMain\Core\Models\Model
{
  public string $table = 'campaigns';
  public string $recordManagerClass = RecordManagers\Campaign::class;
  public ?string $lookupSqlValue = '{%TABLE%}.name';

  public array $relations = [
    'MANAGER' => [ self::BELONGS_TO, User::class, 'id_manager', 'id'],
  ];

  public function describeColumns(): array
  {
    return array_merge(parent::describeColumns(), [
      'name' => (new Varchar($this, $this->translate('Name')))->setRequired()->setProperty('defaultVisibility', true),
      'target_audience' => (new Text($this, $this->translate('Target audience')))->setProperty('defaultVisibility', true),
      'goal' => (new Text($this, $this->translate('Goal')))->setProperty('defaultVisibility', true),
      'notes' => (new Text($this, $this->translate('Notes'))),
      'color' => (new Color($this, $this->translate('Color'))),
      'id_manager' => (new Lookup($this, $this->translate('Manager'), User::class))->setProperty('defaultVisibility', true)->setDefaultValue($this->main->auth->getUserId())->setProperty('defaultVisibility', true),
      'datetime_created' => (new DateTime($this, $this->translate('Created')))->setProperty('defaultVisibility', true)->setRequired()->setDefaultValue(date('Y-m-d H:i:s')),
    ]);
  }

  public function describeTable(): \ADIOS\Core\Description\Table
  {
    $description = parent::describeTable();

    $description->ui['title'] = '';
    $description->ui['addButtonText'] = $this->translate('Add Campaign');
    $description->ui['showHeader'] = true;
    $description->ui['showFulltextSearch'] = true;
    $description->ui['showFooter'] = false;

    return $description;
  }

}
