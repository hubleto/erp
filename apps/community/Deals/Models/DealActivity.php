<?php

namespace HubletoApp\Community\Deals\Models;

use ADIOS\Core\Db\Column\Boolean;
use ADIOS\Core\Db\Column\Date;
use ADIOS\Core\Db\Column\Lookup;
use ADIOS\Core\Db\Column\Time;
use ADIOS\Core\Db\Column\Varchar;
use ADIOS\Core\Description\Form;
use HubletoApp\Community\Contacts\Models\Contact;
use HubletoApp\Community\Customers\Models\Activity;
use HubletoApp\Community\Settings\Models\ActivityType;
use HubletoApp\Community\Settings\Models\User;

class DealActivity extends \HubletoMain\Core\Models\Model
{
  public string $table = 'deal_activities';
  public string $recordManagerClass = RecordManagers\DealActivity::class;

  public array $relations = [
    'DEAL' => [ self::BELONGS_TO, Deal::class, 'id_deal', 'id' ],
  ];

  public function describeColumns(): array
  {
    return array_merge(parent::describeColumns(), [
      'id_deal' => (new Lookup($this, $this->translate('Deal'), Deal::class))->setFkOnUpdate('CASCADE')->setFkOnDelete('SET NULL')->setRequired(),
      'id_contact' => (new Lookup($this, $this->translate('Contact'), Contact::class))->setFkOnUpdate('CASCADE')->setFkOnDelete('SET NULL'),
      'id_activity_type' => (new Lookup($this, $this->translate('Activity type'), ActivityType::class, 'SET NULL')),
      'subject' => (new Varchar($this, $this->translate('Subject')))->setRequired(),
      'date_start' => (new Date($this, $this->translate('Start date')))->setRequired(),
      'time_start' => (new Time($this, $this->translate('Start time'))),
      'date_end' => (new Date($this, $this->translate('End date'))),
      'time_end' => (new Time($this, $this->translate('End time'))),
      'all_day' => (new Boolean($this, $this->translate('All day'))),
      'completed' => (new Boolean($this, $this->translate('Completed'))),
      'id_owner' => (new Lookup($this, $this->translate('Created by'), User::class)),
    ]);
  }

  public function describeForm(): Form
  {
    $description = parent::describeForm();
    $description->defaultValues = [
      "complete" => 0
    ];
    return $description;
  }
}
