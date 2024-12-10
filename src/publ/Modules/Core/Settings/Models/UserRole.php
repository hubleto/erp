<?php

namespace CeremonyCrmApp\Modules\Core\Settings\Models;

class UserRole extends \CeremonyCrmApp\Core\Model
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
  public string $eloquentClass = Eloquent\UserRole::class;
  public ?string $lookupSqlValue = '{%TABLE%}.role';
  public string $translationContext = 'mod.core.settings.models.userRole';

  public array $relations = [
    'PERMISSIONS' => [ self::HAS_MANY, RolePermission::class, 'id_role', 'id'],
  ];

  public function columns(array $columns = []): array
  {
    return parent::columns([
      'role' => [
        'type' => 'varchar',
        'required' => true,
        'title' => $this->translate('Role')
      ],
      'grant_all' => [
        'type' => 'boolean',
        'title' => $this->translate('Grant all permissions')
      ],

    ]);
  }

  public function tableDescribe(array $description = []): array
  {
    $description["model"] = $this->fullName;
    $description = parent::tableDescribe($description);
    $description['ui']['title'] = 'User Roles';
    $description['ui']['addButtonText'] = 'Add User Role';
    $description['ui']['showHeader'] = true;
    $description['ui']['showFooter'] = false;
    return $description;
  }

  public function formDescribe(array $description = []): array
  {
    $description = parent::formDescribe();
    $description['includeRelations'] = ['PERMISSIONS'];
    return $description;
  }
}