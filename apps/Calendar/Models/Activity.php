<?php

namespace Hubleto\App\Community\Calendar\Models;



use Hubleto\Framework\Db\Column\Boolean;
use Hubleto\Framework\Db\Column\Date;
use Hubleto\Framework\Db\Column\Lookup;
use Hubleto\Framework\Db\Column\Time;
use Hubleto\Framework\Db\Column\Varchar;
use Hubleto\App\Community\Settings\Models\ActivityType;
use Hubleto\App\Community\Auth\Models\User;

class Activity extends \Hubleto\Erp\Model
{
  public string $table = 'activities';
  public string $recordManagerClass = RecordManagers\Activity::class;

  public array $relations = [
    'OWNER' => [ self::BELONGS_TO, User::class, 'id_owner', 'id' ],
    'ACTIVITY_TYPE' => [ self::BELONGS_TO, ActivityType::class, 'id_activity_type', 'id' ],
  ];

  public function describeColumns(): array
  {
    return array_merge(parent::describeColumns(), [
      'subject' => (new Varchar($this, $this->translate('Subject')))->setRequired(),
      'id_activity_type' => (new Lookup($this, $this->translate('Activity type'), ActivityType::class, 'SET NULL')),
      'date_start' => (new Date($this, $this->translate('Start date')))->setRequired(),
      'time_start' => (new Time($this, $this->translate('Start time'))),
      'date_end' => (new Date($this, $this->translate('End date'))),
      'time_end' => (new Time($this, $this->translate('End time'))),
      'all_day' => (new Boolean($this, $this->translate('All day'))),
      'completed' => (new Boolean($this, $this->translate('Completed')))->setDefaultValue(0),
      'meeting_minutes_link' => (new Varchar($this, $this->translate('Meeting minutes (link)'))),
      'id_owner' => (new Lookup($this, $this->translate('Created by'), User::class))->setReactComponent('InputUserSelect')->setDefaultValue($this->getService(\Hubleto\Framework\AuthProvider::class)->getUserId()),
    ]);
  }

  public function describeInput(string $columnName): \Hubleto\Framework\Description\Input
  {
    $description = parent::describeInput($columnName);
    switch ($columnName) {
      case 'meeting_minutes_link':
        $description
          ->setReactComponent('InputHyperlink')
        ;
        break;
    }
    return $description;
  }
}
