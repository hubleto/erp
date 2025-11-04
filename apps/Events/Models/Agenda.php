<?php

namespace Hubleto\App\Community\Events\Models;

use Hubleto\Framework\Db\Column\Boolean;
use Hubleto\Framework\Db\Column\Color;
use Hubleto\Framework\Db\Column\Decimal;
use Hubleto\Framework\Db\Column\Date;
use Hubleto\Framework\Db\Column\DateTime;
use Hubleto\Framework\Db\Column\File;
use Hubleto\Framework\Db\Column\Image;
use Hubleto\Framework\Db\Column\Integer;
use Hubleto\Framework\Db\Column\Json;
use Hubleto\Framework\Db\Column\Lookup;
use Hubleto\Framework\Db\Column\Password;
use Hubleto\Framework\Db\Column\Text;
use Hubleto\Framework\Db\Column\Varchar;


class Agenda extends \Hubleto\Erp\Model
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
      'id_event' => (new Lookup($this, $this->translate('Event'), Event::class))->setDefaultVisible(),
      'title' => (new Varchar($this, $this->translate('Title')))->setDefaultVisible()->setIcon(self::COLUMN_NAME_DEFAULT_ICON),
      'topic' => (new Varchar($this, $this->translate('Topic')))->setDefaultVisible(),
      'description' => (new Text($this, $this->translate('Description'))),
      'floor' => (new Varchar($this, $this->translate('Floor')))->setDefaultVisible(),
      'room' => (new Varchar($this, $this->translate('Room')))->setDefaultVisible(),
      'datetime_start' => (new DateTime($this, $this->translate('Start')))->setDefaultValue(date("Y-m-h H:i:s")),
      'datetime_end' => (new DateTime($this, $this->translate('End')))->setDefaultValue(date("Y-m-h H:i:s")),
    ]);
  }

  public function describeTable(): \Hubleto\Framework\Description\Table
  {
    $description = parent::describeTable();
    $description->ui['addButtonText'] = 'Add Agenda';
    $description->ui['showHeader'] = true;
    $description->ui['showFulltextSearch'] = true;
    $description->ui['showFooter'] = false;

    // Uncomment and modify these lines if you want to define table filter for your model
    // $description->ui['filters'] = [
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
