<?php

namespace HubletoApp\Community\Settings\Models;

class Permission extends \HubletoMain\Core\Model
{
  public string $table = 'permissions';
  public string $eloquentClass = Eloquent\Permission::class;
  public ?string $lookupSqlValue = '{%TABLE%}.permission';

  public function columns(array $columns = []): array
  {
    return parent::columns([
      'permission' => [
        'type' => 'varchar',
        'title' => $this->translate('Permission'),
        'show_column' => true
      ],
    ]);
  }

  public function tableDescribe(array $description = []): array
  {
    $description = parent::tableDescribe($description);

    if (is_array($description['ui'])) {
      $description['ui']['title'] = 'Permissions';
      $description['ui']['showHeader'] = false;
      $description['ui']['showFooter'] = false;
    }

    if (is_array($description['permissions'])) {
      $description['permissions']['canCreate'] = false;
      $description['permissions']['canUpdate'] = false;
      $description['permissions']['canDelete'] = false;
    }

    return $description;
  }

  public function formDescribe(array $description = []): array
  {
    $description = parent::formDescribe($description);

    if (is_array($description['ui'])) {
      $description['ui']['title'] = 'Permission';
      $description['ui']['subTitle'] = '';
    }

    if (is_array($description['permissions'])) {
      $description['permissions']['canCreate'] = false;
      $description['permissions']['canUpdate'] = false;
      $description['permissions']['canDelete'] = false;
    }

    return $description;
  }
}
