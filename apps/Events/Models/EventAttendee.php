<?php

namespace HubletoApp\Community\Events\Models;

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

class EventAttendee extends \Hubleto\Framework\Models\Model
{
  public string $table = 'events_has_attendees';
  public string $recordManagerClass = RecordManagers\EventAttendee::class;
  public ?string $lookupSqlValue = 'concat("EventAttendee #", {%TABLE%}.id)';

  public array $relations = [
    'EVENT' => [ self::BELONGS_TO, Event::class, 'id_event', 'id' ],
    'ATTENDEE' => [ self::BELONGS_TO, Attendee::class, 'id_attendee', 'id' ],
  ];

  public function describeColumns(): array
  {
    return array_merge(parent::describeColumns(), [
      'id_event' => (new Lookup($this, $this->translate('Event'), Event::class))->setProperty('defaultVisibility', true),
      'id_attendee' => (new Lookup($this, $this->translate('Attendee'), Attendee::class))->setProperty('defaultVisibility', true),
      'is_attending_virtually' => (new Boolean($this, $this->translate('Is attending virtually')))->setProperty('defaultVisibility', true),
    ]);
  }

  public function describeTable(): \Hubleto\Legacy\Core\Description\Table
  {
    $description = parent::describeTable();
    $description->ui['addButtonText'] = 'Add attendee';
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
