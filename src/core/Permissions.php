<?php

namespace HubletoMain\Core;

use HubletoApp\Community\Settings\Models\Permission;
use HubletoApp\Community\Settings\Models\RolePermission;
use HubletoApp\Community\Settings\Models\UserRole;

class Permissions extends \ADIOS\Core\Permissions {

  public \HubletoMain $main;

  function __construct(\HubletoMain $main)
  {
    $this->main = $main;

    parent::__construct($main);

    if ($this->main->db->isConnected) {
      $this->administratorRoles = $this->loadAdministratorRoles();
    }
  }

  public function loadAdministratorRoles(): array
  {
    $mUserRole = new UserRole($this->main);
    $administratorRoles = (array) $mUserRole->eloquent
      ->where("grant_all", 1)
      ->pluck("id")
      ->toArray()
    ;

    return $administratorRoles;
  }

  /**
  * @return array<int, array<int, string>>
  */
  public function loadPermissions(): array
  {
    $permissions = parent::loadPermissions();

    if ($this->main->db->isConnected) {
      $mUserRole = new UserRole($this->main);

      /** @var array<int, string> */
      $idCommonUserRoles = (array) $mUserRole->eloquent
        ->where("grant_all", 0)
        ->pluck("id")
        ->toArray()
      ;

      foreach ($idCommonUserRoles as $idCommonRole) {
        $idCommonRole = (int) $idCommonRole;

        $mRolePermission = new RolePermission($this->main);

        /** @var array<int, array> */
        $rolePermissions = (array) $mRolePermission->eloquent
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
