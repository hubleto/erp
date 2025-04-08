<?php

namespace HubletoApp\Community\Calendar\Models;

use \ADIOS\Core\Db\Column\Lookup;
use \ADIOS\Core\Db\Column\Varchar;
use \ADIOS\Core\Db\Column\Date;
use \ADIOS\Core\Db\Column\Time;
use \ADIOS\Core\Db\Column\Boolean;
use ADIOS\Core\Description\Form;

use HubletoApp\Community\Contacts\Models\Person;
use HubletoApp\Community\Settings\Models\ActivityType;
use HubletoApp\Community\Settings\Models\User;

class Activity extends \HubletoMain\Core\Model
{
  public string $table = 'activities';
  public string $eloquentClass = Eloquent\Activity::class;

  public array $relations = [];

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
      'completed' => (new Boolean($this, $this->translate('Completed'))),
      'id_user' => (new Lookup($this, $this->translate('Created by'), User::class)),
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
