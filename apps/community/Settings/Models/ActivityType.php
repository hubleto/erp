<?php

namespace HubletoApp\Community\Settings\Models;

class ActivityType extends \HubletoMain\Core\Model
{
  public string $table = 'activity_types';
  public string $eloquentClass = Eloquent\ActivityType::class;
  public ?string $lookupSqlValue = '{%TABLE%}.name';

  public function columnsLegacy(array $columns = []): array
  {
    return parent::columnsLegacy([
      'name' => [
        'type' => 'varchar',
        'title' => $this->translate('Type Name'),
      ],
      'color' => [
        'type' => 'color',
        'title' => $this->translate('Color'),
      ],
      'calendar_visibility' => [
        'type' => 'boolean',
        'title' => $this->translate('Shown in Calendar'),
      ],
      'icon' => [
        'type' => 'varchar',
        'title' => $this->translate('Icon'),
      ]
    ]);
  }

  public function tableDescribe(array $description = []): array
  {
    $description = parent::tableDescribe($description);

    if (is_array($description['ui'])) {
      $description['ui']['title'] = 'Activity Types';
      $description['ui']['addButtonText'] = 'Add Activity Type';
      $description['ui']['showHeader'] = true;
      $description['ui']['showFooter'] = false;
    }

    return $description;
  }

}
