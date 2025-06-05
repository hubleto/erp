<?php

namespace HubletoApp\Community\Customers\Models;

use ADIOS\Core\Db\Column\Boolean;
use ADIOS\Core\Db\Column\Date;
use ADIOS\Core\Db\Column\Lookup;
use ADIOS\Core\Db\Column\Time;
use ADIOS\Core\Db\Column\Varchar;
use HubletoApp\Community\Contacts\Models\Contact;
use HubletoApp\Community\Settings\Models\ActivityType;
use HubletoApp\Community\Settings\Models\User;

class CustomerActivity extends \HubletoMain\Core\Models\Model
{
  public string $table = 'customer_activities';
  public string $recordManagerClass = RecordManagers\CustomerActivity::class;

  // public array $rolePermissions = [
  //   \HubletoApp\Community\Settings\Models\UserRole::ROLE_CHIEF_OFFICER => [ true, true, true, true ],
  //   \HubletoApp\Community\Settings\Models\UserRole::ROLE_MANAGER => [ true, true, true, true ],
  //   \HubletoApp\Community\Settings\Models\UserRole::ROLE_EMPLOYEE => [ true, true, true, false ],
  //   \HubletoApp\Community\Settings\Models\UserRole::ROLE_ASSISTANT => [ true, true, false, false ],
  //   \HubletoApp\Community\Settings\Models\UserRole::ROLE_EXTERNAL => [ false, false, false, false ],
  // ];

  public array $relations = [
    'CUSTOMER' => [ self::BELONGS_TO, Customer::class, 'id_customer', 'id' ],
    'CONTACT' => [ self::BELONGS_TO, Contact::class, 'id_contact', 'id' ],
  ];

  public function describeColumns(): array
  {
    return array_merge(parent::describeColumns(), [
      'id_customer' => (new Lookup($this, $this->translate('Customer'), Customer::class, 'CASCADE'))->setRequired(),
      'id_contact' => (new Lookup($this, $this->translate('Contact'), Contact::class, 'CASCADE')),
      'id_activity_type' => (new Lookup($this, $this->translate('Activity type'), ActivityType::class, 'SET NULL')),
      'subject' => (new Varchar($this, $this->translate('Subject')))->setRequired(),
      'date_start' => (new Date($this, $this->translate('Start Date')))->setRequired(),
      'time_start' => (new Time($this, $this->translate('Start Time'))),
      'date_end' => (new Date($this, $this->translate('End Date'))),
      'time_end' => (new Time($this, $this->translate('End Time'))),
      'all_day' => (new Boolean($this, $this->translate('All day'))),
      'completed' => (new Boolean($this, $this->translate('Completed'))),
      'id_owner' => (new Lookup($this, $this->translate('Created by'), User::class, 'CASCADE')),
    ]);
  }

  public function describeForm(): \ADIOS\Core\Description\Form {

    $describe = parent::describeForm();
    $describe->defaultValues = [
      "id_owner" => $this->main->auth->getUserId(),
      "completed" => 0
    ];

    return $describe;
  }
}
