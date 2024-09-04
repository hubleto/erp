<?php

namespace CeremonyCrmApp\Modules\Core\Customers\Models;

use CeremonyCrmApp\Modules\Core\Settings\Models\Country;

class Address extends \CeremonyCrmApp\Core\Model
{
  public string $fullTableSqlName = 'addresses';
  public string $table = 'addresses';
  public string $eloquentClass = Eloquent\Address::class;
  public ?string $lookupSqlValue = "concat({%TABLE%}.street_line_1, ', ', {%TABLE%}.street_line_2, ', ', {%TABLE%}.city)";

  public array $relations = [
    'PERSON' => [ self::BELONGS_TO, Person::class, "id_person", "id" ],
    'COUNTRY' => [ self::HAS_ONE, Country::class, 'id', 'id_country' ],
  ];

  public function columns(array $columns = []): array
  {
    return parent::columns(array_merge($columns, [
      "id_person" => [
        "type" => "lookup",
        "title" => "Person",
        "model" => "CeremonyCrmApp/Modules/Core/Customers/Models/Person",
        'foreignKeyOnUpdate' => 'CASCADE',
        'foreignKeyOnDelete' => 'CASCADE',
      ],
      "street_line_1" => [
        "type" => "varchar",
        "title" => "Street Line 1",
      ],
      "street_line_2" => [
        "type" => "varchar",
        "title" => "Street Line 2",
      ],
      "region" => [
        "type" => "varchar",
        "title" => "Region",
      ],
      "city" => [
        "type" => "varchar",
        "title" => "City",
      ],
      "postal_code" => [
        "type" => "varchar",
        "title" => "Postal Code",
      ],
      "id_country" => [
        "type" => "lookup",
        "model" => "CeremonyCrmApp/Modules/Core/Settings/Models/Country",
        'foreignKeyOnUpdate' => 'SET NULL',
        'foreignKeyOnDelete' => 'SET NULL',
        "title" => "Country",
      ],
    ]));
  }

  public function tableParams(array $params = []): array
  {
    $params = parent::tableParams();
    $params['title'] = 'Contacts';
    return $params;
  }

}
