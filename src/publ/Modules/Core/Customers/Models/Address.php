<?php

namespace CeremonyCrmApp\Modules\Core\Customers\Models;

use CeremonyCrmApp\Modules\Core\Settings\Models\Country;

class Address extends \CeremonyCrmApp\Core\Model
{
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
        "required" => true,
      ],
      "street_line_1" => [
        "type" => "varchar",
        "title" => "Street Line 1",
        "required" => true,
      ],
      "street_line_2" => [
        "type" => "varchar",
        "title" => "Street Line 2",
        "required" => true,
      ],
      "region" => [
        "type" => "varchar",
        "title" => "Region",
        "required" => true,
      ],
      "city" => [
        "type" => "varchar",
        "title" => "City",
        "required" => true,
      ],
      "postal_code" => [
        "type" => "varchar",
        "title" => "Postal Code",
        "required" => true,
      ],
      "id_country" => [
        "type" => "lookup",
        "model" => "CeremonyCrmApp/Modules/Core/Settings/Models/Country",
        'foreignKeyOnUpdate' => 'SET NULL',
        'foreignKeyOnDelete' => 'SET NULL',
        "title" => "Country",
        "required" => true,
      ],
    ]));
  }

  public function tableDescribe(array $description = []): array
  {
    $description = parent::tableDescribe();
    $description['title'] = 'Contacts';
    return $description;
  }

}
