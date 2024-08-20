<?php

namespace CeremonyCrmApp\Modules\Core\Customers\Models;

class ActivityCategory extends \CeremonyCrmApp\Core\Model
{
  public string $fullTableSqlName = 'activity_categories';
  public string $table = 'activity_categories';
  public string $eloquentClass = Eloquent\ActivityCategory::class;
  public ?string $lookupSqlValue = "{%TABLE%}.name";

  public function columns(array $columns = []): array
  {
    return parent::columns(array_merge($columns, [
      "name" => [
        "type" => "varchar",
        "title" => "Name",
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
    $params['title'] = 'Activity Categories';
    return $params;
  }

}
