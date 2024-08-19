<?php

namespace CeremonyCrmApp\Modules\Core\Customers\Models;

use CeremonyCrmApp\Modules\Core\Settings\Models\User;

class Activity extends \CeremonyCrmApp\Core\Model
{
  public string $fullTableSqlName = 'activities';
  public string $table = 'activities';
  public string $eloquentClass = Eloquent\Activity::class;
  public ?string $lookupSqlValue = "{%TABLE%}.subject";

  public array $relations = [
    'COMPANY' => [ self::HAS_ONE, Company::class, "id_company" ],
    'USER' => [ self::HAS_ONE, User::class, "id_user", "id" ],
    'CATEGORIES' => [ self::HAS_MANY, ActivityCategoryActivity::class, "id_activity", "id" ],
    // 'ATENDEES' => [ self::HAS_MANY, PersonAddress::class, "id_person" ],
    // 'INVITEES' => [ self::BELONGS_TO, Account::class, "id_account", "id" ],
  ];

  public function columns(array $columns = []): array
  {
    return parent::columns(array_merge($columns, [
      "subject" => [
        "type" => "varchar",
        "title" => "Activity subject",
      ],
      "id_company" => [
        "type" => "lookup",
        "title" => "Company",
        "model" => "CeremonyCrmApp/Modules/Core/Customers/Models/Company",
        'foreignKeyOnUpdate' => 'CASCADE',
        'foreignKeyOnDelete' => 'CASCADE',
      ],
      "id_user" => [
        "type" => "lookup",
        "title" => "Created by",
        "model" => "CeremonyCrmApp/Modules/Core/Settings/Models/User",
        "readonly" => true,
        'foreignKeyOnUpdate' => 'CASCADE',
        'foreignKeyOnDelete' => 'CASCADE',
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
