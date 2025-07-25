<?php

namespace HubletoApp\Community\Settings\Models;

use Hubleto\Framework\Db\Column\Boolean;
use Hubleto\Framework\Db\Column\Varchar;

class UserRole extends \Hubleto\Framework\Models\Model
{
  public const ROLE_ADMINISTRATOR = 1;
  public const ROLE_CHIEF_OFFICER = 2;
  public const ROLE_MANAGER = 3;
  public const ROLE_EMPLOYEE = 4;
  public const ROLE_ASSISTANT = 5;
  public const ROLE_EXTERNAL = 6;

  public const USER_ROLES = [
    self::ROLE_ADMINISTRATOR => 'ADMINISTRATOR',
    self::ROLE_CHIEF_OFFICER => 'CHIEF_OFFICER',
    self::ROLE_MANAGER => 'MANAGER',
    self::ROLE_EMPLOYEE => 'EMPLOYEE',
    self::ROLE_ASSISTANT => 'ASSISTANT',
    self::ROLE_EXTERNAL => 'EXTERNAL',
  ];

  public string $table = 'user_roles';
  public string $recordManagerClass = RecordManagers\UserRole::class;
  public ?string $lookupSqlValue = '{%TABLE%}.role';

  // public array $relations = [
  //   'PERMISSIONS' => [ self::HAS_MANY, RolePermission::class, 'id_role', 'id'],
  // ];

  public function describeColumns(): array
  {
    return array_merge(parent::describeColumns(), [
      'role' => (new Varchar($this, $this->translate("Role")))->setRequired(),
      'grant_all' => (new Boolean($this, $this->translate("Grant all permissions (administrator role)"))),
      'description' => (new Varchar($this, $this->translate("Description"))),
      'is_default' => (new Boolean($this, $this->translate("Is default role (cannot be modified)"))),
    ]);
  }

  public function describeTable(): \Hubleto\Framework\Description\Table
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
