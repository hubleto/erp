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

class Speaker extends \HubletoMain\Core\Models\Model
{

  public string $table = 'events_speakers';
  public string $recordManagerClass = RecordManagers\Speaker::class;
  public ?string $lookupSqlValue = 'concat(ifnull({%TABLE%}.full_name, ""), " ", ifnull({%TABLE%}.email, ""))';

  public function describeColumns(): array
  {
    return array_merge(parent::describeColumns(), [
      'salutation' => (new Varchar($this, $this->translate('Salutation')))->setProperty('defaultVisibility', true),
      'title_before' => (new Varchar($this, $this->translate('Title before')))->setProperty('defaultVisibility', true),
      'full_name' => (new Varchar($this, $this->translate('Full name')))->setProperty('defaultVisibility', true),
      'title_after' => (new Varchar($this, $this->translate('Title after')))->setProperty('defaultVisibility', true),
      'short_bio' => (new Varchar($this, $this->translate('Short bio')))->setProperty('defaultVisibility', true),
      'long_bio' => (new Text($this, $this->translate('Long bio')))->setProperty('defaultVisibility', true),
      'email' => (new Varchar($this, $this->translate('Email')))->setProperty('defaultVisibility', true),
      'phone' => (new Varchar($this, $this->translate('Phone')))->setProperty('defaultVisibility', true),
      'social_profile_url_1' => (new Varchar($this, $this->translate('Social profile URL #1'))),
      'social_profile_url_2' => (new Varchar($this, $this->translate('Social profile URL #2'))),
      'social_profile_url_3' => (new Varchar($this, $this->translate('Social profile URL #3'))),
      'social_profile_url_4' => (new Varchar($this, $this->translate('Social profile URL #4'))),
      'social_profile_url_5' => (new Varchar($this, $this->translate('Social profile URL #5'))),
      'notes' => (new Varchar($this, $this->translate('Notes')))->setProperty('defaultVisibility', true),
    ]);
  }

  public function describeTable(): \ADIOS\Core\Description\Table
  {
    $description = parent::describeTable();
    $description->ui['addButtonText'] = 'Add Speaker';
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
