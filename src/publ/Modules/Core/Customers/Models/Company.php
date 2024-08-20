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
    'FIRST_CONTACT' => [ self::HAS_ONE, Person::class, "id_company" ],
    'BUSINESS_ACCOUNT' => [ self::HAS_ONE, BusinessAccount::class, "id_company" ],
    'ACTIVITIES' => [ self::HAS_MANY, Activity::class, "id_company" ],
  ];

  public function columns(array $columns = []): array
  {
    return parent::columns(array_merge($columns, [
      "name" => [
        "type" => "varchar",
        "title" => "Name",
      ],
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
      "vat_id" => [
        "type" => "varchar",
        "title" => "VAT ID",
      ],
      "company_id" => [
        "type" => "varchar",
        "title" => "Company ID",
      ],
      "tax_id" => [
        "type" => "varchar",
        "title" => "Tax ID",
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
