<?php

namespace CeremonyCrmApp\Modules\Core\Sandbox\Models;

use Illuminate\Database\Eloquent\Builder;

class Person extends \CeremonyCrmApp\Core\Model
{
  public string $table = 'sbx_persons';
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

  public function tableDescribe(array $description = []): array
  {
    $description = parent::tableDescribe();
    $description['title'] = 'Persons';
    return $description;
  }

  public function prepareLoadRecordQuery(bool $addLookups = false, $query = null, $level = 0): \Illuminate\Database\Eloquent\Builder
  {
    $query = parent::prepareLoadRecordQuery();

    $query = $query->selectRaw("
      (Select value from person_contacts where id_person = persons.id and type = 'number' LIMIT 1) virt_number,
      (Select value from person_contacts where id_person = persons.id and type = 'email' LIMIT 1) virt_email,
      (Select concat(street_line_1,', ', street_line_2, ', ', city) from person_addresses where id_person = persons.id LIMIT 1) virt_address
    ")
    ;

    //var_dump($this->params); exit;
    /* if ($this->params["idAccount"]) {
      $query = $query->where("join_id_company.id_account");
    } */

    return $query;
  }

}
