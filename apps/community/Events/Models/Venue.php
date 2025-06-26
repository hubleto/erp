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

class Venue extends \HubletoMain\Core\Models\Model
{

  public string $table = 'events_venues';
  public string $recordManagerClass = RecordManagers\Venue::class;
  public ?string $lookupSqlValue = '{%TABLE%}.name';

  public function describeColumns(): array
  {
    return array_merge(parent::describeColumns(), [
      'name' => (new Varchar($this, $this->translate('Name')))->setRequired()->setProperty('defaultVisibility', true),
      'address' => (new Varchar($this, $this->translate('Address')))->setProperty('defaultVisibility', true),
      'address_plus_code' => (new Varchar($this, $this->translate('Address Plus code'))),
      'contact_person' => (new Varchar($this, $this->translate('Contact person'))),
      'contact_email' => (new Varchar($this, $this->translate('Contact email')))->setProperty('defaultVisibility', true),
      'contact_phone' => (new Varchar($this, $this->translate('Contact phone'))),
      'lng' => (new Decimal($this, $this->translate('Coordinates: longitude'))),
      'lat' => (new Decimal($this, $this->translate('Coordinates: latitude'))),
      'description' => (new Text($this, $this->translate('Description'))),
      'capacity' => (new Decimal($this, $this->translate('Capacity')))->setReadonly()->setProperty('defaultVisibility', true)
        ->setDescription('Automatically calculated as total capacity of all locations in warehouse.')
      ,
      'photo_1' => (new Image($this, $this->translate('Photo #1'))),
      'photo_2' => (new Image($this, $this->translate('Photo #2'))),
      'photo_3' => (new Image($this, $this->translate('Photo #3'))),
    ]);
  }

  public function describeTable(): \ADIOS\Core\Description\Table
  {
    $description = parent::describeTable();
    $description->ui['addButtonText'] = 'Add Venue';
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
