<?php

namespace HubletoApp\Community\Leads\Models;

use HubletoApp\Community\Settings\Models\User;

use ADIOS\Core\Db\Column\Color;
use ADIOS\Core\Db\Column\Varchar;
use ADIOS\Core\Db\Column\Text;
use ADIOS\Core\Db\Column\Lookup;

class Campaign extends \HubletoMain\Core\Models\Model
{
  public string $table = 'lead_campaigns';
  public string $recordManagerClass = RecordManagers\Campaign::class;
  public ?string $lookupSqlValue = '{%TABLE%}.name';

  public array $relations = [
    'LEADS' => [ self::HAS_MANY, Lead::class, 'id_campaign', 'id' ],
    'MANAGER' => [ self::BELONGS_TO, User::class, 'id_manager', 'id'],
  ];

  public function describeColumns(): array
  {
    return array_merge(parent::describeColumns(), [
      'name' => (new Varchar($this, $this->translate('Name')))->setRequired(),
      'target_audience' => (new Text($this, $this->translate('Target audience')))->setRequired(),
      'goal' => (new Text($this, $this->translate('Goal')))->setRequired(),
      'color' => (new Color($this, $this->translate('Color'))),
      'id_manager' => (new Lookup($this, $this->translate('Manager'), User::class))->setProperty('defaultVisibility', true)->setDefaultValue($this->main->auth->getUserId()),
    ]);
  }

  public function describeTable(): \ADIOS\Core\Description\Table
  {
    $description = parent::describeTable();

    $description->ui['title'] = '';
    $description->ui['addButtonText'] = 'Add Lead Campaign';
    $description->ui['showHeader'] = true;
    $description->ui['showFulltextSearch'] = true;
    $description->ui['showFooter'] = false;

    return $description;
  }

}
