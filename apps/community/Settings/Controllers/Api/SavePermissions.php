<?php

namespace HubletoApp\Community\Settings\Controllers\Api;

use Exception;
use HubletoApp\Community\Settings\Models\Permission;
use HubletoApp\Community\Settings\Models\RolePermission;
use HubletoApp\Community\Settings\Models\UserRole;

class SavePermissions extends \HubletoMain\Core\Controller
{
  public int $returnType = \ADIOS\Core\Controller::RETURN_TYPE_JSON;
  public function renderJson(): ?array
  {
    $roleId = $this->main->urlParamAsInteger("roleId");
    $rolePermissions = $this->main->urlParamAsArray("permissions");
    $roleTitle = $this->main->urlParamAsString("roleTitle");
    $grantAll = $this->main->urlParamAsBool("grantAll");

    if ($roleId > 0) {
      try {
        $mUserRole = new UserRole($this->main);
        $userRole = $mUserRole->eloquent->find($roleId);
        $userRole->update([
          "role" => $roleTitle,
          "grant_all" => $grantAll
        ]);

        $mRolePermission = new RolePermission($this->main);
        $mRolePermission->eloquent->where("id_role", $roleId)->delete();

        foreach ($rolePermissions as $key => $permissionId) {
          $mRolePermission->eloquent->create([
            "id_role" => $roleId,
            "id_permission" => (int) $permissionId
          ]);
        }
      } catch (Exception $e) {
        return [
          "status" => "failed",
          "error" => $e
        ];
      }
    }

    return [
      "status" => "success",
    ];
  }
}
