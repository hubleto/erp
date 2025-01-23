<?php

namespace HubletoApp\Community\Settings\Models;

class Permission extends \HubletoMain\Core\Model
{
  public string $table = 'permissions';
  public string $eloquentClass = Eloquent\Permission::class;
  public ?string $lookupSqlValue = '{%TABLE%}.allias';

  public function columns(array $columns = []): array
  {
    return parent::columns([
      'permission' => [
        'type' => 'varchar',
        'title' => $this->translate('Permission'),
        'show_column' => true
      ],
      'allias' => [
        'type' => 'varchar',
        'title' => $this->translate('Allias'),
        'show_column' => true
      ],
    ]);
  }

  public function tableDescribe(array $description = []): array
  {
    $description["model"] = $this->fullName;
    $description = parent::tableDescribe($description);
    $description['ui']['title'] = 'Permissions';
    $description['ui']['showHeader'] = false;
    $description['ui']['showFooter'] = false;
    $description['permissions']['canCreate'] = false;
    $description['permissions']['canUpdate'] = false;
    $description['permissions']['canDelete'] = false;
    return $description;
  }

  public function formDescribe(array $description = []): array
  {
    $description = parent::formDescribe();
    $description['ui']['title'] = 'Permission';
    $description['ui']['subTitle'] = '';
    $description['permissions']['canCreate'] = false;
    $description['permissions']['canUpdate'] = false;
    $description['permissions']['canDelete'] = false;
    return $description;
  }
}
