<?php

namespace CeremonyCrmApp\Modules\Core\Settings\Models;

class Label extends \CeremonyCrmApp\Core\Model
{
  public string $table = 'labels';
  public string $eloquentClass = Eloquent\Label::class;
  public ?string $lookupSqlValue = '{%TABLE%}.name';

  public function columns(array $columns = []): array
  {
    return parent::columns(array_merge($columns, [
      'name' => [
        'type' => 'varchar',
        'title' => 'Name',
        'required' => true,
      ],
      'color' => [
        'type' => 'color',
        'title' => 'Color',
        'required' => true,
      ],
    ]));
  }

  public function tableDescribe(array $description = []): array
  {
    $description = parent::tableDescribe();
    $description['title'] = 'Labels';
    return $description;
  }

}
