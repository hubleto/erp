<?php declare(strict_types=1);

namespace Hubleto\App\Community\Settings;

use Hubleto\App\Community\Auth\AuthProvider;
use Hubleto\App\Community\Auth\Models\User;
use Hubleto\App\Community\Settings\Models\RolePermission;
use Hubleto\App\Community\Settings\Models\UserRole;
use Hubleto\Erp\Exceptions;
use Hubleto\Erp\Interfaces;
use Hubleto\Framework\Core;
use Hubleto\Framework\Helper;
use Hubleto\Framework\Model;

/**
 * Class managing Hubleto permissions.
 */
class PermissionsManager extends Core implements Interfaces\PermissionsManagerInterface
{

  protected bool $grantAllPermissions = false;
  protected array $permissionsData = [];
  public array $administratorRoles = [];
  public array $administratorTypes = [];

  protected string $permission = '';

  public function init(): void
  {
    $this->permissionsData = $this->loadPermissions();
    $this->expandPermissionGroups();
    $this->administratorRoles = $this->loadAdministratorRoles();
    $this->administratorTypes = $this->loadAdministratorTypes();
  }

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

  public function getPermission(): string
  {
    return $this->permission;
  }

  public function setPermission(string $permission): void
  {
    $this->permission = $permission;
  }

  public function DANGEROUS__grantAllPermissions(): void
  {
    $this->grantAllPermissions = true;
  }

  public function revokeGrantAllPermissions(): void
  {
    $this->grantAllPermissions = false;
  }

  public function expandPermissionGroups(): void
  {
    foreach ($this->permissionsData as $idUserRole => $permissionsByRole) {
      foreach ($permissionsByRole as $permission) {
        if (strpos($permission, ':') !== FALSE) {
          list($pGroup, $pGroupItems) = explode(':', $permission);
          if (strpos($pGroupItems, ',') !== FALSE) {
            $pGroupItemsArr = explode(',', $pGroupItems);
            if (count($pGroupItemsArr) > 1) {
              foreach ($pGroupItemsArr as $item) {
                $this->permissionsData[$idUserRole][] = $pGroup . ':' . $item;
              }
            }
          }
        }
      }
    }
  }

  public function set(string $permission, int $idUserRole, bool $isEnabled)
  {
    $this->config()->save(
      "permissions/{$idUserRole}/".str_replace("/", ":", $permission),
      $isEnabled ? "1" : "0"
    );
  }

  public function hasRole(int|string $role): bool
  {
    if (is_string($role)) {
      $userRoleModel = $this->createUserRoleModel();
      if ($userRoleModel) {
        /** @disregard P1012 */
        $idUserRoleByRoleName = array_flip($userRoleModel::USER_ROLES);
        $idRole = (int) $idUserRoleByRoleName[$role];
      } else {
        $idRole = 0;
      }
    } else {
      $idRole = (int) $role;
    }

    return in_array($idRole, $this->getService(AuthProvider::class)->getUserRoles());
  }

  public function grantedForRole(string $permission, int|string $userRole): bool
  {
    if (empty($permission)) return TRUE;

    $granted = (bool) in_array($permission, (array) ($this->permissionsData[$userRole] ?? []));

    if (!$granted) {
    }

    return $granted;
  }

  public function granted(string $permission, array $userRoles = [], int $userType = 0): bool
  {
    if ($this->grantAllPermissions) {
      return true;
    } else {
      if (empty($permission)) return true;
      if (count($userRoles) == 0) $userRoles = $this->getService(AuthProvider::class)->getUserRoles();
      if ($userType == 0) $userType = $this->getService(AuthProvider::class)->getUserType();

      $granted = false;

      if (count(array_intersect($this->administratorRoles, $userRoles)) > 0) $granted = true;
      if (in_array($userType, $this->administratorTypes)) $granted = true;

      // check if the premission is granted for one of the roles of the user
      if (!$granted) {
        foreach ($userRoles as $userRole) {
          $granted = $this->grantedForRole($permission, $userRole);
          if ($granted) break;
        }
      }

      // check if the premission is granted "globally" (for each role)
      if (!$granted) {
        $granted = $this->grantedForRole($permission, 0);
      }

      return $granted;
    }

  }

  public function checkPermission(): void
  {
    $this->check($this->permission);
  }

  public function check(string $permission): void
  {
    if (!$this->granted($permission) && !$this->granted(str_replace('\\', '/', $permission))) {
      throw new Exceptions\NotEnoughPermissionsException("Not enough permissions ({$permission}).");
    }
  }

  public function isAppPermittedForActiveUser(\Hubleto\Framework\Interfaces\AppInterface $app): bool
  {
    $userRoles = $this->getService(AuthProvider::class)->getUserRoles();
    $userType = $this->getService(AuthProvider::class)->getUserType();

    if (
      $this->grantAllPermissions
      || $app->permittedForAllUsers
      || in_array($userType, $this->administratorTypes)
      || count(array_intersect($this->administratorRoles, $userRoles)) > 0
    ) {
      return true;
    }

    $user = $this->getService(AuthProvider::class)->getUser();
    $userApps = @json_decode($user['apps'], true);

    return is_array($userApps) && in_array($app->namespace, $userApps);
  }
  
}
