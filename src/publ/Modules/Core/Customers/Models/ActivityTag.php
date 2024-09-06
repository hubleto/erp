<?php

namespace CeremonyCrmApp\Modules\Core\Customers\Models;

class ActivityTag extends \CeremonyCrmApp\Core\Model
{
  public string $table = 'activities_tags';
  public string $eloquentClass = Eloquent\ActivityTag::class;

  public array $relations = [
    'TAG' => [ self::BELONGS_TO, Tag::class, 'id_tag', "id" ],
  ];

  public function columns(array $columns = []): array
  {
    return parent::columns(array_merge($columns, [
      "id_activity" => [
        "type" => "lookup",
        "title" => "Activity",
        "model" => "CeremonyCrmApp/Modules/Core/Customers/Models/Activity",
        'foreignKeyOnUpdate' => 'CASCADE',
        'foreignKeyOnDelete' => 'CASCADE',
      ],
      "id_tag" => [
        "type" => "lookup",
        "title" => "Tag",
        "model" => "CeremonyCrmApp/Modules/Core/Customers/Models/Tag",
        'foreignKeyOnUpdate' => 'CASCADE',
        'foreignKeyOnDelete' => 'CASCADE',
      ],
    ]));
  }

  public function tableDescribe(array $description = []): array
  {
    $description = parent::tableDescribe();
    $description['title'] = 'Activity Categories';
    return $description;
  }

}
