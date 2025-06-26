<?php

namespace HubletoApp\Community\Events\Models;

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

class Event extends \HubletoMain\Core\Models\Model
{

  const ENUM_ATTENDANCE_OPTION_IN_PERSON = 1;
  const ENUM_ATTENDANCE_OPTION_VIRTUAL = 2;
  const ENUM_ATTENDANCE_OPTION_HYBRID = 3;

  const ENUM_ATTENDANCE_OPTIONS = [
    self::ENUM_ATTENDANCE_OPTION_IN_PERSON => 'In-person',
    self::ENUM_ATTENDANCE_OPTION_VIRTUAL => 'Virtual',
    self::ENUM_ATTENDANCE_OPTION_HYBRID => 'Hybrid',
  ];

  public string $table = 'events';
  public string $recordManagerClass = RecordManagers\Event::class;
  public ?string $lookupSqlValue = 'concat("Event #", {%TABLE%}.id)';

  public array $relations = [ 
    'TYPE' => [ self::BELONGS_TO, Type::class, 'id_type', 'id' ],
    'ORGANIZER' => [ self::BELONGS_TO, User::class, 'id_organizer', 'id' ],
  ];

  public function describeColumns(): array
  {
    return array_merge(parent::describeColumns(), [
      'title' => (new Varchar($this, $this->translate('Title')))->setProperty('defaultVisibility', true)->setRequired()->setCssClass('text-2xl text-primary'),
      'id_type' => (new Lookup($this, $this->translate('Type'), Type::class))->setProperty('defaultVisibility', true),
      'attendance_options' => (new Integer($this, $this->translate('Attendance options')))->setProperty('defaultVisibility', true)->setEnumValues(self::ENUM_ATTENDANCE_OPTIONS),
      'brief_description' => (new Text($this, $this->translate('Brief description'))),
      'full_description' => (new Text($this, $this->translate('Full description'))),
      'date_start' => (new Date($this, $this->translate('Date')))->setProperty('defaultVisibility', true)->setRequired()->setDefaultValue(date("Y-m-d")),
      'date_end' => (new Date($this, $this->translate('Date')))->setProperty('defaultVisibility', true)->setRequired()->setDefaultValue(date("Y-m-d")),
      'id_organizer' => (new Lookup($this, $this->translate('Organizer'), User::class))->setProperty('defaultVisibility', true)->setDefaultValue($this->main->auth->getUserId()),
      // 'varchar_example' => (new Varchar($this, $this->translate('Varchar')))->setProperty('defaultVisibility', true)->setReadonly()->setRequired()->setCssClass('text-2xl text-primary'),
      // 'text_example' => (new Text($this, $this->translate('Text')))->setProperty('defaultVisibility', true)->setReadonly()->setRequired()->setCssClass('text-2xl text-primary'),
      // 'decimal_example' => (new Decimal($this, $this->translate('Number')))->setProperty('defaultVisibility', true)->setReadonly()->setRequired()->setCssClass('text-2xl text-primary')
      //   ->setDecimals(4)
      // ,
      // 'date_example' => (new Date($this, $this->translate('Date')))->setProperty('defaultVisibility', true)->setReadonly()->setRequired()
      //   ->setDefaultValue(date("Y-m-d"))
      // ,
      // 'datetime_example' => (new DateTime($this, $this->translate('DateTime')))->setProperty('defaultVisibility', true)->setReadonly()->setRequired()
      //   ->setDefaultValue(date("Y-m-d H:i:s"))
      // ,
      // 'integer_example' => (new Integer($this, $this->translate('Integer')))->setProperty('defaultVisibility', true)->setReadonly()->setRequired()
      //   ->setEnumValues(self::INTEGER_ENUM_VALUES)
      //   ->setEnumCssClasses([
      //     self::ENUM_ONE => 'bg-blue-50',
      //     self::ENUM_TWO => 'bg-yellow-50',
      //     self::ENUM_THREE => 'bg-green-50',
      //   ])
      //   ->setDefaultValue(self::ENUM_ONE)
      // ,
      // 'color_example' => (new Color($this, $this->translate('Color')))->setProperty('defaultVisibility', true)->setReadonly()->setRequired(),
      // 'image_example' => (new Image($this, $this->translate('Image')))->setProperty('defaultVisibility', true)->setReadonly()->setRequired(),
      // 'file_example' => (new File($this, $this->translate('File')))->setProperty('defaultVisibility', true)->setReadonly()->setRequired(),
    ]);
  }

  public function describeTable(): \ADIOS\Core\Description\Table
  {
    $description = parent::describeTable();
    $description->ui['addButtonText'] = 'Add Event';
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
    return parent::onAfterUpdate($originalRecord, $savedRecord);
  }

  public function onAfterCreate(array $savedRecord): array
  {
    return parent::onAfterCreate($savedRecord);
  }

}
