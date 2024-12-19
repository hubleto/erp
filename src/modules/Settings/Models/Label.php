<?php

namespace CeremonyCrmMod\Settings\Models;

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
        'title' => $this->translate('Name'),
        'required' => true,
      ],
      'color' => [
        'type' => 'color',
        'title' => $this->translate('Color'),
        'required' => true,
      ],
    ]));
  }

  public function tableDescribe(array $description = []): array
  {
    $description["model"] = $this->fullName;
    $description = parent::tableDescribe($description);
    $description['ui']['title'] = 'Labels';
    $description['ui']['addButtonText'] = 'Add Label';
    $description['ui']['showHeader'] = true;
    $description['ui']['showFooter'] = false;
    return $description;
  }
}
