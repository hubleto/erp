<?php

namespace CeremonyCrmApp\Modules\Core\Settings\Models;

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
    $description = parent::tableDescribe();
    $description['title'] = 'Deal Statuses';
    return $description;
  }

}
