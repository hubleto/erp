<?php

namespace HubletoApp\Community\Settings\Models;

use ADIOS\Core\Db\Column\Lookup;
use ADIOS\Core\Db\Column\Text;
use ADIOS\Core\Db\Column\Color;
use ADIOS\Core\Db\Column\Varchar;

class Team extends \HubletoMain\Core\Models\Model
{
  public string $table = 'teams';
  public string $recordManagerClass = RecordManagers\Team::class;
  public ?string $lookupSqlValue = '{%TABLE%}.name';

  public function describeColumns(): array
  {
    return array_merge(parent::describeColumns(), [
      'name' => (new Varchar($this, $this->translate("Team name")))->setRequired(),
      'color' => (new Color($this, $this->translate("Team color"))),
      'description' => (new Text($this, $this->translate("Description"))),
      'id_manager' => (new Lookup($this, $this->translate("Manager"), User::class))->setRequired(),
    ]);
  }

  public function describeTable(): \ADIOS\Core\Description\Table
  {
    $description = parent::describeTable();

    $description->ui['title'] = 'Teams';
    $description->ui['addButtonText'] = 'Add team';
    $description->ui['showHeader'] = true;
    $description->ui['showFulltextSearch'] = true;
    $description->ui['showColumnSearch'] = true;
    $description->ui['showFooter'] = false;

    $description->columns = [
      'name' => $description->columns['name'],
      'color' => $description->columns['color'],
      'description' => $description->columns['description'],
      'id_manager' => $description->columns['id_manager'],
      'members' => (new Varchar($this, $this->translate('Members'))),
    ];

    return $description;
  }
}
