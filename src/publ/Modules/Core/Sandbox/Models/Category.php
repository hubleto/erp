<?php

namespace CeremonyCrmApp\Modules\Core\Sandbox\Models;

class Category extends \CeremonyCrmApp\Core\Model
{
  public string $fullTableSqlName = 'categories';
  public string $table = 'categories';
  public string $eloquentClass = Eloquent\Category::class;
  public ?string $lookupSqlValue = "{%TABLE%}.category";

  public function columns(array $columns = []): array
  {
    return parent::columns(array_merge($columns, [
      "category" => [
        "type" => "varchar",
        "title" => "Category",
      ],
      "color" => [
        "type" => "color",
        "title" => "Color",
      ],
    ]));
  }

  public function tableParams(array $params = []): array
  {
    $params = parent::tableParams();
    $params['title'] = 'Categories';
    return $params;
  }

}
