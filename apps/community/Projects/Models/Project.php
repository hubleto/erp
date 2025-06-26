<?php

namespace HubletoApp\Community\Projects\Models;

use \ADIOS\Core\Db\Column\Boolean;
use \ADIOS\Core\Db\Column\Color;
use \ADIOS\Core\Db\Column\Decimal;
use \ADIOS\Core\Db\Column\Date;
use \ADIOS\Core\Db\Column\DateTime;
use \ADIOS\Core\Db\Column\File;
use \ADIOS\Core\Db\Column\Image;
use \ADIOS\Core\Db\Column\Integer;
use \ADIOS\Core\Db\Column\Json;
use \ADIOS\Core\Db\Column\Lookup;
use \ADIOS\Core\Db\Column\Password;
use \ADIOS\Core\Db\Column\Text;
use \ADIOS\Core\Db\Column\Varchar;

use \HubletoApp\Community\Deals\Models\Deal;
use \HubletoApp\Community\Settings\Models\User;

class Project extends \HubletoMain\Core\Models\Model
{

  public string $table = 'projects';
  public string $recordManagerClass = RecordManagers\Project::class;
  public ?string $lookupSqlValue = 'concat("Project #", {%TABLE%}.id)';

  public array $relations = [ 
    'MAIN_DEVELOPER' => [ self::HAS_ONE, User::class, 'id_main_developer', 'id' ],
    'ACCOUNT_MANAGER' => [ self::HAS_ONE, User::class, 'id_account_manager', 'id' ],
    'PHASE' => [ self::HAS_ONE, Phase::class, 'id_phase', 'id' ],
    'OWNER' => [ self::BELONGS_TO, User::class, 'id_owner', 'id' ],
    'MANAGER' => [ self::BELONGS_TO, User::class, 'id_manager', 'id' ],
  ];

  public function describeColumns(): array
  {
    return array_merge(parent::describeColumns(), [
      'id_deal' => (new Lookup($this, $this->translate('Deal'), Deal::class))->setProperty('defaultVisibility', true),
      'title' => (new Varchar($this, $this->translate('Title')))->setProperty('defaultVisibility', true)->setRequired()->setCssClass('text-2xl text-primary'),
      'identifier' => (new Varchar($this, $this->translate('Identifier')))->setProperty('defaultVisibility', true)->setRequired()->setCssClass('text-2xl text-primary'),
      'description' => (new Text($this, $this->translate('Description'))),
      'id_main_developer' => (new Lookup($this, $this->translate('Main developer'), User::class))->setProperty('defaultVisibility', true)->setRequired()
        ->setDefaultValue($this->main->auth->getUserId())
      ,
      'id_account_manager' => (new Lookup($this, $this->translate('Account manager'), User::class))->setProperty('defaultVisibility', true)->setRequired()
        ->setDefaultValue($this->main->auth->getUserId())
      ,
      'id_phase' => (new Lookup($this, $this->translate('Phase'), Phase::class))->setProperty('defaultVisibility', true)->setRequired()
        ->setDefaultValue($this->main->auth->getUserId())
      ,
      'color' => (new Color($this, $this->translate('Color')))->setProperty('defaultVisibility', true),
      'online_documentation_folder' => (new Varchar($this, "Online documentation folder"))->setReactComponent('InputHyperlink'),
      'notes' => (new Text($this, $this->translate('Notes'))),
    ]);
  }

  public function describeTable(): \ADIOS\Core\Description\Table
  {
    $description = parent::describeTable();
    $description->ui['addButtonText'] = 'Add Project';
    $description->ui['showHeader'] = true;
    $description->ui['showFulltextSearch'] = true;
    $description->ui['showFooter'] = false;

    $mPhase = new Phase($this->main);
    $fPhaseOptions = [ ];//0 => 'All' ];
    foreach ($mPhase->record->orderBy('order', 'asc')->get()?->toArray() as $phase) {
      $fPhaseOptions[$phase['id']] = $phase['name'];
    }

    $description->ui['defaultFilters'] = [
      'fPhase' => [ 'title' => 'Phase', 'type' => 'multipleSelectButtons', 'options' => $fPhaseOptions ],
    ];

    return $description;
  }

  public function onBeforeCreate(array $record): array
  {
    return parent::onBeforeCreate($record);
  }

  public function onBeforeUpdate(array $record): array
  {
    return parent::onBeforeUpdate($record);
  }

  public function onAfterUpdate(array $originalRecord, array $savedRecord): array
  {
    return parent::onAfterUpdate($originalRecord, $savedRecord);
  }

  public function onAfterCreate(array $savedRecord): array
  {
    return parent::onAfterCreate($savedRecord);
  }

}
