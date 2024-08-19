<?php

namespace CeremonyCrmApp\Modules\Core\Customers\Models;

class Company extends \CeremonyCrmApp\Core\Model
{
  public string $fullTableSqlName = 'companies';
  public string $table = 'companies';
  public string $eloquentClass = Eloquent\Company::class;
  public ?string $lookupSqlValue = "{%TABLE%}.name";

  public array $relations = [
    'PERSONS' => [ self::HAS_MANY, Person::class, "id_company" ],
    'ACCOUNT' => [ self::BELONGS_TO, Account::class,'id_account', 'id'],
    'BUSINESS_ACCOUNT' => [ self::HAS_ONE, BusinessAccount::class, "id_company" ],
  ];

  public function columns(array $columns = []): array
  {
    return parent::columns(array_merge($columns, [
      "name" => [
        "type" => "varchar",
        "title" => "Name",
      ],
      "id_account" => [
        "type" => "lookup",
        "title" => "Account",
        "model" => "CeremonyCrmApp/Modules/Core/Customers/Models/Account",
        'foreignKeyOnUpdate' => 'CASCADE',
        'foreignKeyOnDelete' => 'CASCADE',
      ],
      /* "tags" => [
        "type" => "tags",
        "title" => "Tags",
      ], */
      "street" => [
        "type" => "varchar",
        "title" => "Street",
      ],
      "city" => [
        "type" => "varchar",
        "title" => "City",
      ],
      "country" => [
        "type" => "varchar",
        "title" => "Country",
      ],
      "postal_code" => [
        "type" => "varchar",
        "title" => "Postal Code",
      ],
    ]));
  }

  public function tableParams(array $params = []): array
  {
    $params = parent::tableParams();
    $params['title'] = 'Companies';
    return $params;
  }

  public function getNewRecordDataFromString(string $text): array {
    return [
      'name' => $text,
    ];
  }

}
