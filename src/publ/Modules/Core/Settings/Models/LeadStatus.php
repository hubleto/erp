<?php

namespace CeremonyCrmApp\Modules\Core\Settings\Models;

class LeadStatus extends \CeremonyCrmApp\Core\Model
{
  public string $table = 'lead_statuses';
  public string $eloquentClass = Eloquent\LeadStatus::class;
  public ?string $lookupSqlValue = '{%TABLE%}.name';
  public string $translationContext = 'mod.core.settings.models.leadStatus';

  public function columns(array $columns = []): array
  {
    return parent::columns(array_merge($columns, [
      'name' => [
        'type' => 'varchar',
        'title' => 'Name',
        'required' => true,
      ],
      'order' => [
        'type' => 'int',
        'title' => 'Order',
        'required' => true,
      ],
      'color' => [
        'type' => 'color',
        'title' => 'Color',
        'required' => false,
      ],
    ]));
  }

  public function tableDescribe(array $description = []): array
  {
    $description["model"] = $this->fullName;
    $description = parent::tableDescribe($description);
    $description['ui']['title'] = 'Lead Statuses';
    $description['ui']['addButtonText'] = 'Add Lead Status';
    $description['ui']['showHeader'] = true;
    $description['ui']['showFooter'] = false;
    return $description;
  }
}
