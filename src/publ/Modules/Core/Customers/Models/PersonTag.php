<?php

namespace CeremonyCrmApp\Modules\Core\Customers\Models;

class PersonTag extends \CeremonyCrmApp\Core\Model
{
  public string $table = 'persons_tags';
  public string $eloquentClass = Eloquent\PersonTag::class;

  public array $relations = [
    'TAG' => [ self::BELONGS_TO, Tag::class, 'id_tag', "id" ],
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
      "id_tag" => [
        "type" => "lookup",
        "title" => "Tag",
        "model" => "CeremonyCrmApp/Modules/Core/Customers/Models/Tag",
        'foreignKeyOnUpdate' => 'CASCADE',
        'foreignKeyOnDelete' => 'CASCADE',
        "required" => true,
      ],
    ]));
  }

  public function tableDescribe(array $description = []): array
  {
    $description = parent::tableDescribe();
    $description['title'] = 'Person Categories';
    return $description;
  }

}
