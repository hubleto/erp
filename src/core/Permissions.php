<?php

namespace CeremonyCrmApp\Core;

use CeremonyCrmMod\Core\Settings\Models\Permission;
use CeremonyCrmMod\Core\Settings\Models\RolePermission;
use CeremonyCrmMod\Core\Settings\Models\UserRole;

class Permissions extends \ADIOS\Core\Permissions {

  function __construct(\ADIOS\Core\Loader $app)
  {
    parent::__construct($app);

    if ($this->app->db) {
      $this->administratorRoles = $this->loadAdministratorRoles();
    }
  }

  public function loadAdministratorRoles(): array {
    $mUserRole = new UserRole($this->app);
    $administratorRoles = $mUserRole->eloquent
      ->where("grant_all", 1)
      ->pluck("id")
      ->toArray()
    ;

    return $administratorRoles;
  }

  public function loadPermissions(): array {
    $permissions = parent::loadPermissions();

    if ($this->app->db) {
      $mUserRole = new UserRole($this->app);
      $idCommonUserRoles = $mUserRole->eloquent
        ->where("grant_all", 0)
        ->pluck("id")
        ->toArray()
      ;

      foreach ($idCommonUserRoles as $idCommonRole) {
        $mRolePermission = new RolePermission($this->app);
        $rolePermissions = $mRolePermission->eloquent
          ->selectRaw("role_permissions.*,permissions.permission")
          ->where("id_role", $idCommonRole)
          ->join("permissions", "role_permissions.id_permission", "permissions.id")
          ->get()
        ;

        foreach ($rolePermissions as $key => $rolePermission) {
          $permissions[$idCommonRole][] = $rolePermission->permission;
        }
      }
    }

    return $permissions;
  }
}
