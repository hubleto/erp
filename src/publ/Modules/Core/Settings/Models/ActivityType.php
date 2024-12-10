<?php

namespace CeremonyCrmApp\Modules\Core\Settings\Models;

class ActivityType extends \CeremonyCrmApp\Core\Model
{
  public string $table = 'activity_types';
  public string $eloquentClass = Eloquent\ActivityType::class;
  public ?string $lookupSqlValue = '{%TABLE%}.name';
  public string $translationContext = 'mod.core.settings.models.activityType';

  public function columns(array $columns = []): array
  {
    return parent::columns([
      'name' => [
        'type' => 'varchar',
        $this->translate('Type Name'),
      ],
      'color' => [
        'type' => 'color',
        'title' => $this->translate('Color'),
      ],
      'calendar_visibility' => [
        'type' => 'boolean',
        $this->translate('Shown in Calendar'),
      ],
      'icon' => [
        'type' => 'varchar',
        $this->translate('Icon'),
      ]
    ]);
  }

  public function tableDescribe(array $description = []): array
  {
    $description["model"] = $this->fullName;
    $description = parent::tableDescribe($description);
    $description['ui']['title'] = 'Activity Types';
    $description['ui']['addButtonText'] = 'Add Activity Type';
    $description['ui']['showHeader'] = true;
    $description['ui']['showFooter'] = false;
    return $description;
  }

}
