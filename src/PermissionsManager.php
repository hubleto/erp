<?php declare(strict_types=1);

namespace Hubleto\Erp;

use Hubleto\App\Community\Settings\Models\RolePermission;
use Hubleto\App\Community\Settings\Models\User;
use Hubleto\App\Community\Settings\Models\UserRole;

use \Hubleto\Framework\Helper;

/**
 * Class managing Hubleto permissions.
 */
class PermissionsManager extends \Hubleto\Framework\PermissionsManager
{

  public function createUserRoleModel(): \Hubleto\Framework\Model
  {
    return $this->getModel(\Hubleto\App\Community\Settings\Models\UserRole::class);
  }

  public function createRolePermissionModel(): Model
  {
    return $this->getModel(\Hubleto\App\Community\Settings\Models\RolePermission::class);
  }

  public function loadAdministratorRoles(): array
  {
    if (!$this->db()->isConnected) {
      return [];
    }
    $mUserRole = $this->getService(UserRole::class);
    $administratorRoles = Helper::pluck('id', $this->db()->fetchAll("select id from `{$mUserRole->table}` where grant_all = 1"));
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
    foreach ($this->config()->getAsArray('permissions') as $idUserRole => $permissionsByRole) {
      $permissions[$idUserRole] = [];
      foreach ($permissionsByRole as $permissionPath => $isEnabled) {
        if ((bool) $isEnabled) {
          $permissions[$idUserRole][] = str_replace(":", "/", $permissionPath);
        }
      }
      $permissions[$idUserRole] = array_unique($permissions[$idUserRole]);
    }

    if ($this->db()->isConnected) {
      $mUserRole = $this->getService(UserRole::class);

      $idCommonUserRoles = Helper::pluck('id', $this->db()->fetchAll("select id from `{$mUserRole->table}` where grant_all = 0"));

      foreach ($idCommonUserRoles as $idCommonRole) {
        $idCommonRole = (int) $idCommonRole;

        $mRolePermission = $this->getService(RolePermission::class);

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
