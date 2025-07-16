<?php

namespace HubletoApp\Community\Calendar\Models;

use ADIOS\Core\Db\Column\Boolean;
use ADIOS\Core\Db\Column\Date;
use ADIOS\Core\Db\Column\Lookup;
use ADIOS\Core\Db\Column\Time;
use ADIOS\Core\Db\Column\Varchar;
use HubletoApp\Community\Settings\Models\ActivityType;
use HubletoApp\Community\Settings\Models\User;

class SharedCalendar extends \HubletoMain\Core\Models\Model
{
  public string $table = 'shared_calendars';
  public string $recordManagerClass = RecordManagers\SharedCalendar::class;

  public array $relations = [
    'OWNER' => [ self::BELONGS_TO, User::class, 'id_owner', 'id' ],
  ];

  public function describeColumns(): array
  {
    return array_merge(parent::describeColumns(), [
      'id_owner' => (new Lookup($this, $this->translate('Created by'), User::class))->setDefaultValue($this->app->auth->getUserId()),
      'calendar' => (new Varchar($this, $this->translate('Subject')))->setRequired(),
      'share_key' => (new Varchar($this, $this->translate('Subject')))->setRequired(),
//      'view_details' => (new Boolean($this, $this->translate('Completed')))->setDefaultValue(0),
//      'enabled' => (new Boolean($this, $this->translate('Completed')))->setDefaultValue(true),

      // implement stuff like date from or date until
    ]);
  }
}
