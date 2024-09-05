<?php

namespace CeremonyCrmApp\Modules\Core\Customers\Models;

use CeremonyCrmApp\Modules\Core\Settings\Models\User;

class Activity extends \CeremonyCrmApp\Core\Model
{
  public string $table = 'activities';
  public string $eloquentClass = Eloquent\Activity::class;
  public ?string $lookupSqlValue = "{%TABLE%}.subject";

  public array $relations = [
    'COMPANY' => [ self::HAS_ONE, Company::class, "id_company", "id" ],
    'USER' => [ self::HAS_ONE, User::class, "id_user", "id" ],
    'TAGS' => [ self::HAS_MANY, ActivityTag::class, "id_activity", "id" ],
    'ATENDANCE' => [ self::HAS_MANY, Atendance::class, "id_activity", "id" ],
    // 'INVITEES' => [ self::BELONGS_TO, Account::class, "id_account", "id" ],
  ];

  public function columns(array $columns = []): array
  {
    return parent::columns(array_merge($columns, [
      "subject" => [
        "type" => "varchar",
        "title" => "Activity subject",
        "required" => true,
      ],
      "due_date" => [
        "type" => "date",
        "title" => "Due date",
        "required" => true,
      ],
      "due_time" => [
        "type" => "time",
        "title" => "Due time",
        "required" => true,
      ],
      "duration" => [
        "type" => "time",
        "title" => "Duration",
        "required" => false,
      ],
      "completed" => [
        "type" => "boolean",
        "title" => "Completed",
      ],
      "id_company" => [
        "type" => "lookup",
        "title" => "Company",
        "model" => "CeremonyCrmApp/Modules/Core/Customers/Models/Company",
        'foreignKeyOnUpdate' => 'CASCADE',
        'foreignKeyOnDelete' => 'CASCADE',
        "required" => false,
      ],
      "id_user" => [
        "type" => "lookup",
        "title" => "Created by",
        "model" => "CeremonyCrmApp/Modules/Core/Settings/Models/User",
        'foreignKeyOnUpdate' => 'CASCADE',
        'foreignKeyOnDelete' => 'CASCADE',
        "required" => false,
      ],
    ]));
  }

  public function tableParams(array $params = []): array
  {
    $params = parent::tableParams();
    $params['title'] = 'Activities';
    return $params;
  }

}
