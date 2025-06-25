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

use \HubletoApp\Community\Settings\Models\User;

class Project extends \HubletoMain\Core\Models\Model
{

  const ENUM_ONE = 1;
  const ENUM_TWO = 2;
  const ENUM_THREE = 3;

  const INTEGER_ENUM_VALUES = [
    self::ENUM_ONE => 'One',
    self::ENUM_TWO => 'Two',
    self::ENUM_THREE => 'Three',
  ]

  public string $table = 'projects';
  public string $recordManagerClass = RecordManagers\Project::class;
  public ?string $lookupSqlValue = 'concat("Project #", {%TABLE%}.id)';

  public array $relations = [ 
    'OWNER' => [ self::BELONGS_TO, User::class, 'id_owner', 'id' ]
    'MANAGER' => [ self::BELONGS_TO, User::class, 'id_manager', 'id' ]
  ];

  public function describeColumns(): array
  {
    return array_merge(parent::describeColumns(), [
      'varchar_example' => (new Varchar($this, $this->translate('Varchar')))->setProprty('defaultVisibility', true)->setReadonly()->setRequired(),
      'decimal_example' => (new Decimal($this, $this->translate('Number')))->setProprty('defaultVisibility', true)->setReadonly()->setRequired()
        ->setDecimals(4)
      ,
      'date_example' => (new Date($this, $this->translate('Date')))->setProprty('defaultVisibility', true)->setReadonly()->setRequired()
        ->setDefaultValue(date("Y-m-d"))
      ,
      'datetime_example' => (new DateTime($this, $this->translate('DateTime')))->setProprty('defaultVisibility', true)->setReadonly()->setRequired()
        ->setDefaultValue(date("Y-m-d H:i:s"))
      ,
      'integer_example' => (new Integer($this, $this->translate('Integer')))->setProprty('defaultVisibility', true)->setReadonly()->setRequired()
        ->setEnumValues(self::INTEGER_ENUM_VALUES)
        ->setEnumCssClasses([
          self::ENUM_ONE => 'bg-blue-50',
          self::ENUM_TWO => 'bg-yellow-50',
          self::ENUM_THREE => 'bg-green-50',
        ])
        ->setDefaultValue(self::ENUM_ONE)
      ,
      'image_example' => (new Image($this, $this->translate('Image')))->setProprty('defaultVisibility', true)->setReadonly()->setRequired(),
      'file_example' => (new File($this, $this->translate('File')))->setProprty('defaultVisibility', true)->setReadonly()->setRequired(),
      'id_owner' => (new Lookup($this, $this->translate('Owner'), User::class))->setProprty('defaultVisibility', true)->setReadonly()->setRequired()
        ->setDefaultValue($this->main->auth->getUserId())
      ,
      'id_manager' => (new Lookup($this, $this->translate('Manager'), User::class))->setProprty('defaultVisibility', true)->setReadonly()->setRequired()
        ->setDefaultValue($this->main->auth->getUserId())
      ,
    ]);
  }

  public function describeTable(): \ADIOS\Core\Description\Table
  {
    $description = parent::describeTable();
    $description->ui['addButtonText'] = 'Add Project';
    $description->ui['showHeader'] = true;
    $description->ui['showFulltextSearch'] = true;
    $description->ui['showFooter'] = false;

    // Uncomment and modify these lines if you want to define defaultFilter for your model
    // $description->ui['defaultFilters'] = [
    //   'fArchive' => [ 'title' => 'Archive', 'options' => [ 0 => 'Active', 1 => 'Archived' ] ],
    // ];

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
    return parent::onAfterUpdate($savedRecord);
  }

  public function onAfterCreate(array $savedRecord): array
  {
    return parent::onAfterCreate($savedRecord);
  }

}
