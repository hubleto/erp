<?php

namespace HubletoApp\Community\Projects\Models;

use Hubleto\Legacy\Core\Db\Column\Boolean;
use Hubleto\Legacy\Core\Db\Column\Color;
use Hubleto\Legacy\Core\Db\Column\Decimal;
use Hubleto\Legacy\Core\Db\Column\Date;
use Hubleto\Legacy\Core\Db\Column\DateTime;
use Hubleto\Legacy\Core\Db\Column\File;
use Hubleto\Legacy\Core\Db\Column\Image;
use Hubleto\Legacy\Core\Db\Column\Integer;
use Hubleto\Legacy\Core\Db\Column\Json;
use Hubleto\Legacy\Core\Db\Column\Lookup;
use Hubleto\Legacy\Core\Db\Column\Password;
use Hubleto\Legacy\Core\Db\Column\Text;
use Hubleto\Legacy\Core\Db\Column\Varchar;
use HubletoApp\Community\Settings\Models\User;

class Phase extends \Hubleto\Framework\Models\Model
{
  public const ENUM_ONE = 1;
  public const ENUM_TWO = 2;
  public const ENUM_THREE = 3;

  public const INTEGER_ENUM_VALUES = [
    self::ENUM_ONE => 'One',
    self::ENUM_TWO => 'Two',
    self::ENUM_THREE => 'Three',
  ];

  public string $table = 'projects_phases';
  public string $recordManagerClass = RecordManagers\Phase::class;
  public ?string $lookupSqlValue = '{%TABLE%}.name';

  public function describeColumns(): array
  {
    return array_merge(parent::describeColumns(), [
      'name' => (new Varchar($this, $this->translate('Name')))->setProperty('defaultVisibility', true)->setRequired(),
      'order' => (new Integer($this, $this->translate('Order')))->setProperty('defaultVisibility', true)->setRequired(),
      'color' => (new Color($this, $this->translate('Color')))->setProperty('defaultVisibility', true),
    ]);
  }

  public function describeTable(): \Hubleto\Legacy\Core\Description\Table
  {
    $description = parent::describeTable();
    $description->ui['addButtonText'] = 'Add Phase';
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
