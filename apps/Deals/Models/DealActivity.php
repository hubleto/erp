<?php

namespace HubletoApp\Deals\Models;

use HubletoApp\Customers\Models\Activity;

class DealActivity extends \HubletoCore\Core\Model
{
  public string $table = 'deal_activities';
  public string $eloquentClass = Eloquent\DealActivity::class;

  public array $relations = [
    'DEAL' => [ self::BELONGS_TO, Deal::class, 'id_deal', 'id' ],
  ];

  public function columns(array $columns = []): array
  {
    return parent::columns(array_merge($columns, [
      'id_deal' => [
        'type' => 'lookup',
        'title' => 'Deal',
        'model' => 'HubletoApp/Deals/Models/Deal',
        'foreignKeyOnUpdate' => 'CASCADE',
        'foreignKeyOnDelete' => 'CASCADE',
        'required' => true,
        'readonly'=> true,
      ],
      'id_person' => [
        'type' => 'lookup',
        'title' => 'Contact Person',
        'model' => \HubletoApp\Customers\Models\Person::class,
        'foreignKeyOnUpdate' => 'CASCADE',
        'foreignKeyOnDelete' => 'CASCADE',
      ],
      'id_activity_type' => [
        'type' => 'lookup',
        'title' => $this->translate('Activity type'),
        'model' => \HubletoApp\Settings\Models\ActivityType::class,
        'foreignKeyOnUpdate' => 'SET NULL',
        'foreignKeyOnDelete' => 'SET NULL',
        'required' => false,
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
        'model' => \HubletoApp\Settings\Models\User::class,
        'foreignKeyOnUpdate' => 'CASCADE',
        'foreignKeyOnDelete' => 'CASCADE',
        'required' => false,
      ],
    ]));
  }
}
