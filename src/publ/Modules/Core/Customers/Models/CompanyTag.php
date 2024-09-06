<?php

namespace CeremonyCrmApp\Modules\Core\Customers\Models;

class CompanyTag extends \CeremonyCrmApp\Core\Model
{
  public string $table = 'companies_tags';
  public string $eloquentClass = Eloquent\CompanyTag::class;

  public array $relations = [
    'TAG' => [ self::BELONGS_TO, Tag::class, 'id_tag', "id" ],
  ];

  public function columns(array $columns = []): array
  {
    return parent::columns(array_merge($columns, [
      "id_company" => [
        "type" => "lookup",
        "title" => "Company",
        "model" => "CeremonyCrmApp/Modules/Core/Customers/Models/Company",
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
    $description['title'] = 'Company Categories';
    return $description;
  }

}
