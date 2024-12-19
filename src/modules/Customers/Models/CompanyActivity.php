<?php

namespace CeremonyCrmMod\Customers\Models;

use CeremonyCrmMod\Customers\Models\Company;

class CompanyActivity extends \CeremonyCrmApp\Core\Model
{
  public string $table = 'company_activities';
  public string $eloquentClass = Eloquent\CompanyActivity::class;

  public array $relations = [
    'COMPANY' => [ self::BELONGS_TO, Company::class, 'id_company', 'id' ],
  ];

  public function columns(array $columns = []): array
  {
    return parent::columns(array_merge($columns, [
      'id_company' => [
        'type' => 'lookup',
        'title' => 'Company',
        'model' => \CeremonyCrmMod\Customers\Models\Company::class,
        'foreignKeyOnUpdate' => 'CASCADE',
        'foreignKeyOnDelete' => 'CASCADE',
        'required' => true,
        'readonly'=> true,
      ],
      'id_activity_type' => [
        'type' => 'lookup',
        'title' => $this->translate('Activity type'),
        'model' => \CeremonyCrmMod\Settings\Models\ActivityType::class,
        'foreignKeyOnUpdate' => 'SET NULL',
        'foreignKeyOnDelete' => 'SET NULL',
        'required' => true,
      ],
      'subject' => [
        'type' => 'varchar',
        'title' => $this->translate('Subject'),
        'required' => true,
      ],
      'date_start' => [
        'type' => 'date',
        'title' => 'Start Date',
        'required' => true,
      ],
      'time_start' => [
        'type' => 'time',
        'title' => 'Start Time',
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
      'id_user' => [
        'type' => 'lookup',
        'title' => 'Created by',
        'model' => \CeremonyCrmMod\Settings\Models\User::class,
        'foreignKeyOnUpdate' => 'CASCADE',
        'foreignKeyOnDelete' => 'CASCADE',
        'required' => false,
      ],
    ]));
  }
}
