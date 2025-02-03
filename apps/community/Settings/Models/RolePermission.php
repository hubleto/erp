<?php

namespace HubletoApp\Community\Settings\Models;

use \ADIOS\Core\Db\Column\Lookup;

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
      'id_permission' => (new Lookup($this, $this->translate('Permission'), Permission::class))->setRequired(),
      'id_role' => (new Lookup($this, $this->translate('Role'), UserRole::class))->setRequired(),
    ]));
  }

  public function describeTable(): \ADIOS\Core\Description\Table
  {
    $description = parent::describeTable();

    $description->ui['title'] = 'Role Permissions';
    $description->ui['showHeader'] = false;
    $description->ui['showFooter'] = false;

    return $description;
  }

}
