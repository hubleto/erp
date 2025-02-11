<?php

namespace HubletoApp\Community\Customers\Models;

use HubletoApp\Community\Customers\Models\Company;
use HubletoApp\Community\Customers\Models\Person;
use HubletoApp\Community\Settings\Models\ActivityType;
use HubletoApp\Community\Settings\Models\User;

use \ADIOS\Core\Db\Column\Time;
use \ADIOS\Core\Db\Column\Lookup;
use \ADIOS\Core\Db\Column\Varchar;
use \ADIOS\Core\Db\Column\Text;
use \ADIOS\Core\Db\Column\Boolean;
use \ADIOS\Core\Db\Column\Date;

class CompanyActivity extends \HubletoMain\Core\Model
{
  public string $table = 'company_activities';
  public string $eloquentClass = Eloquent\CompanyActivity::class;

  public array $relations = [
    'COMPANY' => [ self::BELONGS_TO, Company::class, 'id_company', 'id' ],
  ];

  public function describeColumns(): array
  {
    return array_merge(parent::describeColumns(), [
      'id_company' => (new Lookup($this, $this->translate('Company'), Company::class, 'CASCADE'))->setRequired()->setReadonly(),
      'id_person' => (new Lookup($this, $this->translate('Contact Person'), Person::class, 'CASCADE')),
      'id_activity_type' => (new Lookup($this, $this->translate('Contact Person'), ActivityType::class, 'SET NULL'))->setRequired(),
      'subject' => (new Varchar($this, $this->translate('Subject')))->setRequired(),
      'date_start' => (new Date($this, $this->translate('Start Date')))->setRequired(),
      'time_start' => (new Time($this, $this->translate('Start Time'))),
      'date_end' => (new Date($this, $this->translate('End Date'))),
      'time_end' => (new Time($this, $this->translate('End Time'))),
      'all_day' => (new Boolean($this, $this->translate('All day'))),
      'completed' => (new Boolean($this, $this->translate('Completed'))),
      'id_user' => (new Lookup($this, $this->translate('Created by'), User::class, 'CASCADE')),
    ]);
  }
}
