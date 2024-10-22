<?php

namespace CeremonyCrmApp\Modules\Core\Sandbox\Models;

class Company extends \CeremonyCrmApp\Core\Model
{
  public string $table = 'sbx_companies';
  public string $eloquentClass = Eloquent\Company::class;
  public ?string $lookupSqlValue = "{%TABLE%}.name";

  public array $relations = [
    'MAIN_PERSON' => [ self::HAS_ONE, Person::class, 'id_company' ],
    'OTHER_PERSONS' => [ self::HAS_MANY, Person::class, 'id_company' ],
    'CATEGORIES' => [ self::HAS_MANY, CompanyCategory::class, 'id_company' ],
  ];

  public function columns(array $columns = []): array
  {
    return parent::columns(array_merge($columns, [
      "name" => [
        "type" => "varchar",
        "title" => "Name",
      ],
    ]));
  }

  public function tableDescribe(array $description = []): array
  {
    $description = parent::tableDescribe();
    $description['title'] = 'Companies';
    return $description;
  }

  public function getNewRecordDataFromString(string $text): array
  {
    return [
      'name' => $text,
    ];
  }

}
