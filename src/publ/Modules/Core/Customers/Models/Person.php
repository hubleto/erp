<?php

namespace CeremonyCrmApp\Modules\Core\Customers\Models;

class Person extends \ADIOS\Core\Model
{
  public string $fullTableSqlName = 'persons';
  public string $table = 'persons';
  public string $eloquentClass = Eloquent\Person::class;
  public ?string $lookupSqlValue = "concat({%TABLE%}.first_name, ' ', {%TABLE%}.last_name)";

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
    ]));
  }

  public function tableParams(array $params = []): array {
    $params = parent::tableParams();
    $params['title'] = 'Persons';
    return $params;
  }

}
