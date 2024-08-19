<?php

namespace CeremonyCrmApp\Modules\Core\Customers\Models;

class Account extends \CeremonyCrmApp\Core\Model
{
  public string $fullTableSqlName = 'accounts';
  public string $table = 'accounts';
  public string $eloquentClass = Eloquent\Account::class;
  public ?string $lookupSqlValue = "{%TABLE%}.name";

  public array $relations = [
    'PERSONS' => [ self::HAS_MANY, Person::class, "id_account" ],
    'COMPANIES' => [ self::HAS_MANY, Company::class,'id_account'],
  ];

  public function columns(array $columns = []): array
  {
    return parent::columns(array_merge($columns, [
      "name" => [
        "type" => "varchar",
        "title" => "Name",
      ],
      /* "tags" => [
        "type" => "tags",
        "title" => "Tags",
      ], */
    ]));
  }

  public function tableParams(array $params = []): array
  {
    $params = parent::tableParams();
    $params['title'] = 'Account';
    return $params;
  }

}
