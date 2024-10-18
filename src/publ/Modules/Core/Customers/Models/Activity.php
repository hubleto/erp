<?php

namespace CeremonyCrmApp\Modules\Core\Customers\Models;

use CeremonyCrmApp\Modules\Core\Settings\Models\ActivityType;
use CeremonyCrmApp\Modules\Core\Settings\Models\User;

class Activity extends \CeremonyCrmApp\Core\Model
{
  public string $table = 'activities';
  public string $eloquentClass = Eloquent\Activity::class;
  public ?string $lookupSqlValue = '{%TABLE%}.subject';

  public array $relations = [
    'COMPANY' => [ self::BELONGS_TO, Company::class, 'id_company', 'id' ],
    'USER' => [ self::BELONGS_TO, User::class, 'id_user', 'id' ],
    'ACTIVITY_TYPE' => [ self::HAS_ONE, ActivityType::class, 'id', 'id_activity_type'],
    // 'ATENDANCE' => [ self::HAS_MANY, Atendance::class, 'id_activity', 'id' ],
    // 'INVITEES' => [ self::BELONGS_TO, Account::class, 'id_account', 'id' ],
  ];

  public function columns(array $columns = []): array
  {
    return parent::columns(array_merge($columns, [
      'id_activity_type' => [
        'type' => 'lookup',
        'title' => 'Type',
        'model' => 'CeremonyCrmApp/Modules/Core/Settings/Models/ActivityType',
        'foreignKeyOnUpdate' => 'SET NULL',
        'foreignKeyOnDelete' => 'SET NULL',
        'required' => true,
      ],
      'subject' => [
        'type' => 'varchar',
        'title' => 'Activity subject',
        'required' => true,
      ],
      'date_start' => [
        'type' => 'date',
        'title' => 'Start Date',
        'required' => true,
      ],
      'time_start' => [
        'type' => 'time',
        'title' => 'Time Start',
        'required' => false,
      ],
      'date_end' => [
        'type' => 'date',
        'title' => 'End Date',
        'required' => false,
      ],
      'time_end' => [
        'type' => 'time',
        'title' => 'End Time',
        'required' => false,
      ],
      'all_day' => [
        'type' => 'boolean',
        'title' => 'All day',
        'required' => false,
      ],
      'completed' => [
        'type' => 'boolean',
        'title' => 'Completed',
        'required' => false,
      ],
      'id_company' => [
        'type' => 'lookup',
        'title' => 'Company',
        'model' => 'CeremonyCrmApp/Modules/Core/Customers/Models/Company',
        'foreignKeyOnUpdate' => 'CASCADE',
        'foreignKeyOnDelete' => 'CASCADE',
        'required' => false,
      ],
      'id_user' => [
        'type' => 'lookup',
        'title' => 'Created by',
        'model' => 'CeremonyCrmApp/Modules/Core/Settings/Models/User',
        'foreignKeyOnUpdate' => 'CASCADE',
        'foreignKeyOnDelete' => 'CASCADE',
        'required' => false,
      ],
    ]));
  }

  public function tableDescribe(array $description = []): array
  {
    $description = parent::tableDescribe();
    $description['ui']['title'] = 'Activities';
    $description['ui']['addButtonText'] = 'Add Activity';
    $description['ui']['showHeader'] = true;
    return $description;
  }

}
