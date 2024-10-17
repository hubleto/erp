<?php

namespace CeremonyCrmApp\Modules\Core\Settings\Models;

class ActivityType extends \CeremonyCrmApp\Core\Model
{
  public string $table = 'activity_types';
  public string $eloquentClass = Eloquent\ActivityType::class;
  public ?string $lookupSqlValue = '{%TABLE%}.name';

  public function columns(array $columns = []): array
  {
    return parent::columns([
      'name' => [
        'type' => 'varchar',
        'title' => 'Type Name',
      ],
      'color' => [
        'type' => 'color',
        'title' => 'Color',
      ],
      'calendar_visibility' => [
        'type' => 'boolean',
        'title' => 'Shown in Calendar',
      ],
      'icon' => [
        'type' => 'varchar',
        'title' => 'Icon',
      ]
    ]);
  }

  public function tableDescribe(array $description = []): array
  {
    $description = parent::tableDescribe();
    $description['ui']['title'] = 'Activity Types';
    $description['ui']['addButtonText'] = 'Add Activity Type';
    $description['ui']['showHeader'] = true;
    $description['ui']['showFooter'] = false;
    return $description;
  }

}
