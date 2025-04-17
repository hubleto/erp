<?php

namespace HubletoApp\Community\Settings\Models;

use ADIOS\Core\Db\Column\Boolean;
use ADIOS\Core\Db\Column\Varchar;

class UserRole extends \HubletoMain\Core\Models\Model
{
  const ROLE_ADMINISTRATOR = 1;
  const ROLE_SALES_MANAGER = 2;
  const ROLE_ACCOUNTANT = 3;

  const USER_ROLES = [
    self::ROLE_ADMINISTRATOR => 'ADMINISTRATOR',
    self::ROLE_SALES_MANAGER => 'SALES_MANAGER',
    self::ROLE_ACCOUNTANT => 'ACCOUNTANT',
  ];

  public string $table = 'user_roles';
  public string $recordManagerClass = RecordManagers\UserRole::class;
  public ?string $lookupSqlValue = '{%TABLE%}.role';

  public array $relations = [
    'PERMISSIONS' => [ self::HAS_MANY, RolePermission::class, 'id_role', 'id'],
  ];

  public function describeColumns(): array
  {
    return array_merge(parent::describeColumns(), [
      'role' => (new Varchar($this, $this->translate("Role")))->setRequired(),
      'grant_all' => (new Boolean($this, $this->translate("Grant all permissions (admin role)"))),
    ]);
  }

  public function describeTable(): \ADIOS\Core\Description\Table
  {
    $description = parent::describeTable();

    $description->ui['title'] = 'User Roles';
    $description->ui['addButtonText'] = 'Add User Role';
    $description->ui['showHeader'] = true;
    $description->ui['showFulltextSearch'] = true;
    $description->ui['showFooter'] = false;

    return $description;
  }

}