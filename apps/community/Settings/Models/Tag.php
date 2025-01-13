<?php

namespace HubletoApp\Community\Settings\Models;

class Tag extends \HubletoMain\Core\Model
{
  public string $table = 'tags';
  public string $eloquentClass = Eloquent\Tag::class;
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
    $description['ui']['title'] = 'Tags';
    $description['ui']['addButtonText'] = 'Add Tag';
    $description['ui']['showHeader'] = true;
    $description['ui']['showFooter'] = false;

    return $description;
  }

}
