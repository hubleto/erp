<?php

namespace CeremonyCrmApp\Modules\Core\Settings\Models;

class Setting extends \CeremonyCrmApp\Core\Model
{
  public string $table = 'settings';
  public string $eloquentClass = Eloquent\Setting::class;
  public string $translationContext = 'mod.core.settings.models.setting';

  public function columns(array $columns = []): array
  {
    return parent::columns([
      'key' => [
        'type' => 'varchar',
        'byte_size' => '250',
        'title' => 'Key',
        'show_column' => true
      ],
      'value' => [
        'type' => 'text',
        'interface' => 'plain_text',
        'title' => 'Value',
        'show_column' => true
      ],
      'id_user' => [
        'type' => 'lookup',
        'title' => 'Only for user',
        'model' => 'CeremonyCrmApp/Modules/Core/Settings/Models/User',
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
    $description["model"] = $this->fullName;
    $description = parent::tableDescribe($description);
    $description['ui']['title'] = 'Settings';
    $description['ui']['addButtonText'] = 'Add Setting';
    $description['ui']['showHeader'] = true;
    $description['ui']['showFooter'] = false;
    return $description;
  }
}
