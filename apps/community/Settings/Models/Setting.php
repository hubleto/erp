<?php

namespace HubletoApp\Community\Settings\Models;

class Setting extends \HubletoMain\Core\Model
{
  public string $table = 'settings';
  public string $eloquentClass = Eloquent\Setting::class;

  public function columns(array $columns = []): array
  {
    return parent::columns([
      'key' => [
        'type' => 'varchar',
        'byte_size' => '250',
        'title' => $this->translate('Key'),
        'show_column' => true
      ],
      'value' => [
        'type' => 'text',
        'interface' => 'plain_text',
        'title' => $this->translate('Value'),
        'show_column' => true
      ],
      'id_user' => [
        'type' => 'lookup',
        'title' => $this->translate('Only for user'),
        'model' => User::class,
        'foreignKeyOnUpdate' => 'RESTRICT',
        'foreignKeyOnDelete' => 'RESTRICT',
        'required' => false,
      ],
    ]);
  }

  public function indexes(array $indexes = [])
  {
    return parent::indexes([
      'key' => [
        'type' => 'unique',
        'columns' => [
          'key' => [
            'order' => 'asc',
          ],
        ],
      ],
    ]);
  }

  public function tableDescribe(array $description = []): array
  {
    $description = parent::tableDescribe($description);
    $description['ui']['title'] = 'Settings';
    $description['ui']['addButtonText'] = 'Add Setting';
    $description['ui']['showHeader'] = true;
    $description['ui']['showFooter'] = false;
    return $description;
  }
}
