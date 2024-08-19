<?php

namespace CeremonyCrmApp\Modules\Core\Customers\Models;

class Contact extends \CeremonyCrmApp\Core\Model
{
  public string $fullTableSqlName = 'person_contacts';
  public string $table = 'person_contacts';
  public string $eloquentClass = Eloquent\Contact::class;
  public ?string $lookupSqlValue = "{%TABLE%}.value";
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
      "type" => [
        "type" => "varchar",
        "title" => "Type",
        "enumValues" => ["email" => "Email", "number" => "Phone Number"],
      ],
      "value" => [
        "type" => "varchar",
        "title" => "Value",
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
