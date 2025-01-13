<?php

namespace HubletoMain\Core;

use HubletoApp\Settings\Models\Permission;
use HubletoApp\Settings\Models\RolePermission;
use HubletoApp\Settings\Models\UserRole;

class Permissions extends \ADIOS\Core\Permissions {

  public \HubletoMain $main;

  function __construct(\ADIOS\Core\Loader $main)
  {
    $this->main = $main;

    parent::__construct($main);

    if ($this->main->db) {
      $this->administratorRoles = $this->loadAdministratorRoles();
    }
  }

  public function loadAdministratorRoles(): array {
    $mUserRole = new UserRole($this->main);
    $administratorRoles = $mUserRole->eloquent
      ->where("grant_all", 1)
      ->pluck("id")
      ->toArray()
    ;

    return $administratorRoles;
  }

  public function loadPermissions(): array {
    $permissions = parent::loadPermissions();

    if ($this->main->db) {
      $mUserRole = new UserRole($this->main);
      $idCommonUserRoles = $mUserRole->eloquent
        ->where("grant_all", 0)
        ->pluck("id")
        ->toArray()
      ;

      foreach ($idCommonUserRoles as $idCommonRole) {
        $mRolePermission = new RolePermission($this->main);
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
