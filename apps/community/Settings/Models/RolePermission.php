<?php

namespace HubletoApp\Community\Settings\Models;

use \ADIOS\Core\Db\Column\Lookup;

class RolePermission extends \HubletoMain\Core\Model
{
  public string $table = 'role_permissions';
  public string $recordManagerClass = RecordManagers\RolePermission::class;

  public array $relations = [
    'ROLE' => [ self::BELONGS_TO, UserRole::class, 'id_role', 'id' ],
    'PERMISSION' => [ self::BELONGS_TO, Permission::class, 'id_permission', 'id' ],
  ];

  public function describeColumns(): array
  {
    return array_merge(parent::describeColumns(), [
      'id_permission' => (new Lookup($this, $this->translate('Permission'), Permission::class))->setRequired(),
      'id_role' => (new Lookup($this, $this->translate('Role'), UserRole::class))->setRequired(),
    ]);
  }

  public function describeTable(): \ADIOS\Core\Description\Table
  {
    $description = parent::describeTable();

    $description->ui['title'] = 'Role Permissions';
    $description->ui['showHeader'] = false;
    $description->ui['showFulltextSearch'] = true;
    $description->ui['showFooter'] = false;

    return $description;
  }

}
