<?php

namespace CeremonyCrmApp\Modules\Core\Customers\Models;

use CeremonyCrmApp\Modules\Core\Settings\Models\User;

class Atendance extends \CeremonyCrmApp\Core\Model
{
  public string $fullTableSqlName = 'atendance';
  public string $table = 'atendance';
  public string $eloquentClass = Eloquent\Atendance::class;
  public ?string $lookupSqlValue = "{%TABLE%}.id_user";

  public array $relations = [
    'ACTIVITY' => [ self::HAS_ONE, Activity::class, "id_activity", "id" ],
    'USER' => [ self::HAS_ONE, User::class, "id_user", "id" ],
  ];

  public function columns(array $columns = []): array
  {
    return parent::columns(array_merge($columns, [
      "id_activity" => [
        "type" => "lookup",
        "title" => "Activity",
        "model" => "CeremonyCrmApp/Modules/Core/Customers/Models/Activity",
        'foreignKeyOnUpdate' => 'CASCADE',
        'foreignKeyOnDelete' => 'CASCADE',
        "required" => false,
      ],
      "id_user" => [
        "type" => "lookup",
        "title" => "Atendee",
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
    $params['title'] = 'Activity Atendance';
    return $params;
  }

}
