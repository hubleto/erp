<?php

namespace CeremonyCrmApp\Modules\Core\Customers\Models;

class Person extends \CeremonyCrmApp\Core\Model
{
  public string $fullTableSqlName = 'persons';
  public string $table = 'persons';
  public string $eloquentClass = Eloquent\Person::class;
  public ?string $lookupSqlValue = "concat({%TABLE%}.first_name, ' ', {%TABLE%}.last_name)";

  public array $relations = [
    'COMPANY' => [ self::BELONGS_TO, Company::class, "id" ],
    'CONTACTS' => [ self::HAS_MANY, Contact::class, "id_person" ],
    'ADDRESSES' => [ self::HAS_MANY, Address::class, "id_person" ],
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
        'foreignKeyOnUpdate' => 'CASCADE',
        'foreignKeyOnDelete' => 'CASCADE',
      ],
      "is_primary" => [
        "type" => "boolean",
        "title" => "First Contact",
      ],
      "note" => [
        "type" => "text",
        "title" => "Notes",
      ],
      "is_active" => [
        "type" => "boolean",
        "title" => "Active",
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
