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

class Agenda extends \HubletoMain\Core\Models\Model
{

  public string $table = 'events_agendas';
  public string $recordManagerClass = RecordManagers\Agenda::class;
  public ?string $lookupSqlValue = '{%TABLE%}.title';

  public array $relations = [ 
    'EVENT' => [ self::BELONGS_TO, Event::class, 'id_event', 'id' ],
  ];

  public function describeColumns(): array
  {
    return array_merge(parent::describeColumns(), [
      'id_event' => (new Lookup($this, $this->translate('Event'), Event::class))->setProperty('defaultVisibility', true),
      'title' => (new Varchar($this, $this->translate('Title')))->setProperty('defaultVisibility', true),
      'topic' => (new Varchar($this, $this->translate('Topic')))->setProperty('defaultVisibility', true),
      'description' => (new Text($this, $this->translate('Description'))),
      'floor' => (new Varchar($this, $this->translate('Floor')))->setProperty('defaultVisibility', true),
      'room' => (new Varchar($this, $this->translate('Room')))->setProperty('defaultVisibility', true),
      'datetime_start' => (new DateTime($this, $this->translate('Start')))->setDefaultValue(date("Y-m-h H:i:s")),
      'datetime_end' => (new DateTime($this, $this->translate('End')))->setDefaultValue(date("Y-m-h H:i:s")),
    ]);
  }

  public function describeTable(): \ADIOS\Core\Description\Table
  {
    $description = parent::describeTable();
    $description->ui['addButtonText'] = 'Add Agenda';
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
