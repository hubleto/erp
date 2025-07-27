<?php

namespace HubletoApp\Community\Calendar\Models;

use Hubleto\Framework\Db\Column\Boolean;
use Hubleto\Framework\Db\Column\Date;
use Hubleto\Framework\Db\Column\Lookup;
use Hubleto\Framework\Db\Column\Time;
use Hubleto\Framework\Db\Column\Varchar;
use HubletoApp\Community\Settings\Models\ActivityType;
use HubletoApp\Community\Settings\Models\User;

class SharedCalendar extends \Hubleto\Framework\Models\Model
{
  public string $table = 'shared_calendars';
  public string $recordManagerClass = RecordManagers\SharedCalendar::class;

  public array $relations = [
    'OWNER' => [ self::BELONGS_TO, User::class, 'id_owner', 'id' ],
  ];

  public function describeColumns(): array
  {
    return array_merge(parent::describeColumns(), [
      'id_owner' => (new Lookup($this, $this->translate('Created by'), User::class))->setDefaultValue($this->main->auth->getUserId())->setReadonly(),
      'calendar' => (new Varchar($this, $this->translate('Calendar ID')))->setRequired()->setReadonly(),
      'share_key' => (new Varchar($this, $this->translate('Share key')))->setReadonly()->setHidden(),
      'view_details' => (new Boolean($this, $this->translate('Display details')))->setDefaultValue(true),
      'enabled' => (new Boolean($this, $this->translate('Enabled')))->setDefaultValue(true),
      'date_from' => (new Date($this, $this->translate('Date from'))),
      'date_to' => (new Date($this, $this->translate('Date to'))),

      // implement stuff like date from or date until
    ]);
  }

  public function onBeforeCreate(array $record): array
  {
    $record['share_key'] = bin2hex(random_bytes(10));
    return $record;
  }

  public function onAfterUpdate(array $originalRecord, array $savedRecord): array
  {
    $mSharedCalendar = new RecordManagers\SharedCalendar();
    $model = $mSharedCalendar->where('id', $savedRecord['id'])->first();

    if ($savedRecord['date_to'] == "") {
      $model->update([
        'date_to' => null
      ]);
    }
    if ($savedRecord['date_from'] == "") {
      $model->update([
        'date_from' => null
      ]);
    }

    $model->save();

    return parent::onAfterUpdate($originalRecord, $savedRecord);
  }
}
