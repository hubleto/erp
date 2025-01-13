<?php

namespace HubletoApp\Community\Settings\Models;

class RolePermission extends \HubletoMain\Core\Model
{
  public string $table = 'role_permissions';
  public string $eloquentClass = Eloquent\RolePermission::class;

  public array $relations = [
    'ROLE' => [ self::BELONGS_TO, UserRole::class, 'id_role', 'id' ],
    'PERMISSION' => [ self::BELONGS_TO, Permission::class, 'id_permission', 'id' ],
  ];

  public function columns(array $columns = []): array
  {
    return parent::columns(array_merge($columns, [
      'id_permission' => [
        'type' => 'lookup',
        'title' => $this->translate('Permission'),
        'model' => Permission::class,
        'foreignKeyOnUpdate' => 'CASCADE',
        'foreignKeyOnDelete' => 'CASCADE',
        'required' => true,
      ],
      'id_role' => [
        'type' => 'lookup',
        'title' => $this->translate('Role'),
        'model' => UserRole::class,
        'foreignKeyOnUpdate' => 'CASCADE',
        'foreignKeyOnDelete' => 'CASCADE',
        'required' => true,
      ],
    ]));
  }

  public function tableDescribe(array $description = []): array
  {
    $description["model"] = $this->fullName;
    $description = parent::tableDescribe($description);
    $description['ui']['title'] = 'Role Permissions';
    $description['ui']['showHeader'] = false;
    $description['ui']['showFooter'] = false;
    return $description;
  }

}
