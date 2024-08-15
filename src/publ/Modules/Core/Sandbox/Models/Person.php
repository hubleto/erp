<?php

namespace CeremonyCrmApp\Modules\Core\Sandbox\Models;

class Person extends \CeremonyCrmApp\Core\Model
{
  public string $fullTableSqlName = 'persons';
  public string $table = 'persons';
  public string $eloquentClass = Eloquent\Person::class;
  public ?string $lookupSqlValue = "concat({%TABLE%}.first_name, ' ', {%TABLE%}.last_name)";

  public array $relations = [
    'COMPANY' => [ self::BELONGS_TO, Company::class ]
  ];

  public function columns(array $columns = []): array
  {
    return parent::columns(array_merge($columns, [
      "first_name" => [
        "type" => "varchar",
        "title" => "First name",
      ],
      "last_name" => [
        "type" => "varchar",
        "title" => "Last name",
      ],
      "id_company" => [
        "type" => "lookup",
        "title" => "Company",
        "model" => "CeremonyCrmApp/Modules/Core/Customers/Models/Company",
      ],
      "is_main" => [
        "type" => "boolean",
        "title" => "Is main person",
      ],
    ]));
  }

  public function tableParams(array $params = []): array
  {
    $params = parent::tableParams();
    $params['title'] = 'Persons';
    return $params;
  }

}
