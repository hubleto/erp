<?php

namespace CeremonyCrmMod\Settings\Models;

class DealStatus extends \CeremonyCrmApp\Core\Model
{
  public string $table = 'deal_statuses';
  public string $eloquentClass = Eloquent\DealStatus::class;
  public ?string $lookupSqlValue = '{%TABLE%}.name';

  public function columns(array $columns = []): array
  {
    return parent::columns(array_merge($columns, [
      'name' => [
        'type' => 'varchar',
        'title' => $this->translate('Name'),
        'required' => true,
      ],
      'order' => [
        'type' => 'int',
        'title' => $this->translate('Order'),
        'required' => true,
      ],
      'color' => [
        'type' => 'color',
        'title' => $this->translate('Color'),
        'required' => false,
      ],
    ]));
  }

  public function tableDescribe(array $description = []): array
  {
    $description["model"] = $this->fullName;
    $description = parent::tableDescribe($description);
    $description['ui']['title'] = 'Deal Statuses';
    $description['ui']['addButtonText'] = 'Add Deal Status';
    $description['ui']['showHeader'] = true;
    $description['ui']['showFooter'] = false;
    return $description;
  }
}
