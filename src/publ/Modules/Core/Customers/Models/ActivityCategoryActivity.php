<?php

namespace CeremonyCrmApp\Modules\Core\Customers\Models;

class ActivityCategoryActivity extends \CeremonyCrmApp\Core\Model
{
  public string $fullTableSqlName = 'activity_categories_activities';
  public string $table = 'activity_categories_activities';
  public string $eloquentClass = Eloquent\ActivityCategoryActivity::class;

  public array $relations = [
    'CATEGORY' => [ self::BELONGS_TO, ActivityCategory::class, 'id_category', "id" ],
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
      "id_activity_category" => [
        "type" => "lookup",
        "title" => "Activity Category",
        "model" => "CeremonyCrmApp/Modules/Core/Customers/Models/ActivityCategory",
        'foreignKeyOnUpdate' => 'CASCADE',
        'foreignKeyOnDelete' => 'CASCADE',
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
