<?php

namespace CeremonyCrmApp\Modules\Core\Customers\Models;

class Address extends \CeremonyCrmApp\Core\Model
{
  public string $fullTableSqlName = 'person_addresses';
  public string $table = 'person_addresses';
  public string $eloquentClass = Eloquent\Address::class;
  public ?string $lookupSqlValue = "concat({%TABLE%}.street, ', ', {%TABLE%}.city)";
  //public ?string $lookupSqlValue = "concat({%TABLE%}.value, ' - ', {%TABLE%}.type)";

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
      "street" => [
        "type" => "varchar",
        "title" => "Street",
      ],
      "city" => [
        "type" => "varchar",
        "title" => "City",
      ],
      "postal_code" => [
        "type" => "varchar",
        "title" => "Postal Code",
      ],
      "country" => [
        "type" => "varchar",
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
