<?php declare(strict_types=1);

namespace HubletoMain;

use HubletoApp\Community\Settings\Models\Permission;
use HubletoApp\Community\Settings\Models\RolePermission;
use HubletoApp\Community\Settings\Models\User;
use HubletoApp\Community\Settings\Models\UserRole;

use \Hubleto\Framework\Helper;

/**
 * Class managing Hubleto permissions.
 */
class Permissions extends \Hubleto\Framework\Permissions
{

  public function createUserRoleModel(): \Hubleto\Framework\Model
  {
    return new \HubletoApp\Community\Settings\Models\UserRole($this->main);
  }

  public function loadAdministratorRoles(): array
  {
    if (!isset($this->main->pdo) || !$this->main->pdo->isConnected) {
      return [];
    }
    $mUserRole = $this->main->di->create(UserRole::class);
    $administratorRoles = Helper::pluck('id', $this->main->pdo->fetchAll("select id from `{$mUserRole->table}` where grant_all = 1"));
    return $administratorRoles;
  }

  public function loadAdministratorTypes(): array
  {
    return [
      User::TYPE_ADMINISTRATOR,
    ];
  }

  /**
  * @return array<int, array<int, string>>
  */
  public function loadPermissions(): array
  {
    $permissions = [];
    foreach ($this->main->config->getAsArray('permissions') as $idUserRole => $permissionsByRole) {
      $permissions[$idUserRole] = [];
      foreach ($permissionsByRole as $permissionPath => $isEnabled) {
        if ((bool) $isEnabled) {
          $permissions[$idUserRole][] = str_replace(":", "/", $permissionPath);
        }
      }
      $permissions[$idUserRole] = array_unique($permissions[$idUserRole]);
    }

    if (isset($this->main->pdo) && $this->main->pdo->isConnected) {
      $mUserRole = $this->main->di->create(UserRole::class);

      $idCommonUserRoles = Helper::pluck('id', $this->main->pdo->fetchAll("select id from `{$mUserRole->table}` where grant_all = 0"));

      foreach ($idCommonUserRoles as $idCommonRole) {
        $idCommonRole = (int) $idCommonRole;

        $mRolePermission = $this->main->di->create(RolePermission::class);

        /** @var array<int, array> */
        $rolePermissions = (array) $mRolePermission->record
          ->selectRaw("role_permissions.*,permissions.permission")
          ->where("id_role", $idCommonRole)
          ->join("permissions", "role_permissions.id_permission", "permissions.id")
          ->get()
          ->toArray()
        ;

        foreach ($rolePermissions as $key => $rolePermission) {
          $permissions[$idCommonRole][] = (string) $rolePermission['permission'];
        }
      }
    }

    return $permissions;
  }
  
}
