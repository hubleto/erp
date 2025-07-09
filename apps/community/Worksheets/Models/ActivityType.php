<?php

namespace HubletoApp\Community\Worksheets\Models;

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

class ActivityType extends \HubletoMain\Core\Models\Model
{

  public string $table = 'worksheet_activities_types';
  public string $recordManagerClass = RecordManagers\ActivityType::class;
  public ?string $lookupSqlValue = 'concat("ActivityType #", {%TABLE%}.id)';

  public function describeColumns(): array
  {
    return array_merge(parent::describeColumns(), [
      'name' => (new Varchar($this, $this->translate('Varchar')))->setProperty('defaultVisibility', true),
      'color' => (new Color($this, $this->translate('Color')))->setProperty('defaultVisibility', true),
    ]);
  }

  public function describeTable(): \ADIOS\Core\Description\Table
  {
    $description = parent::describeTable();
    $description->ui['addButtonText'] = 'Add ActivityType';
    $description->ui['showHeader'] = true;
    $description->ui['showFulltextSearch'] = true;
    $description->ui['showFooter'] = false;

    // Uncomment and modify these lines if you want to define defaultFilter for your model
    // $description->ui['defaultFilters'] = [
    //   'fArchive' => [ 'title' => 'Archive', 'options' => [ 0 => 'Active', 1 => 'Archived' ] ],
    // ];

    return $description;
  }

  // public function onBeforeCreate(array $record): array
  // {
  //   return parent::onBeforeCreate($record);
  // }

  // public function onBeforeUpdate(array $record): array
  // {
  //   return parent::onBeforeUpdate($record);
  // }

  // public function onAfterUpdate(array $originalRecord, array $savedRecord): array
  // {
  //   return parent::onAfterUpdate($originalRecord, $savedRecord);
  // }

  // public function onAfterCreate(array $savedRecord): array
  // {
  //   return parent::onAfterCreate($savedRecord);
  // }

}
