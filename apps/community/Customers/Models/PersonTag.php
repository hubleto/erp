<?php

namespace HubletoApp\Community\Customers\Models;

use HubletoApp\Community\Settings\Models\Tag;

class PersonTag extends \HubletoMain\Core\Model
{
  public string $table = 'person_tags';
  public string $eloquentClass = Eloquent\PersonTag::class;

  public array $relations = [
    'TAG' => [ self::BELONGS_TO, Tag::class, 'id_tag', 'id' ],
    'PERSON' => [ self::BELONGS_TO, Person::class, 'id_person', 'id' ],
  ];

  public function columnsLegacy(array $columns = []): array
  {
    return parent::columnsLegacy(array_merge($columns, [
      'id_person' => [
        'type' => 'lookup',
        'title' => 'Person',
        'model' => 'HubletoApp/Community/Customers/Models/Person',
        'foreignKeyOnUpdate' => 'CASCADE',
        'foreignKeyOnDelete' => 'CASCADE',
        'required' => true,
      ],
      'id_tag' => [
        'type' => 'lookup',
        'title' => 'Tag',
        'model' => 'HubletoApp/Community/Settings/Models/Tag',
        'foreignKeyOnUpdate' => 'CASCADE',
        'foreignKeyOnDelete' => 'CASCADE',
        'required' => true,
      ],
    ]));
  }

  public function tableDescribe(array $description = []): array
  {
    $description["model"] = $this->fullName;
    $description = parent::tableDescribe($description);
    $description['title'] = 'Person Categories';
    return $description;
  }

}
