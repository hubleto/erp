<?php

namespace HubletoApp\Community\Settings\Controllers\Api;

use Exception;
use HubletoApp\Community\Settings\Models\RolePermission;
use HubletoApp\Community\Settings\Models\UserRole;

class SavePermissions extends \HubletoMain\Controllers\ApiController
{
  public function renderJson(): ?array
  {
    $roleId = $this->getRouter()->urlParamAsInteger("roleId");
    $rolePermissions = $this->getRouter()->urlParamAsArray("permissions");
    $roleTitle = $this->getRouter()->urlParamAsString("roleTitle");
    $grantAll = $this->getRouter()->urlParamAsBool("grantAll");

    if ($roleId > 0) {
      try {
        $mUserRole = $this->getService(UserRole::class);
        $userRole = $mUserRole->record->find($roleId);
        $userRole->update([
          "role" => $roleTitle,
          "grant_all" => $grantAll
        ]);

        $mRolePermission = $this->getService(RolePermission::class);
        $mRolePermission->record->where("id_role", $roleId)->delete();

        foreach ($rolePermissions as $key => $permissionId) {
          $mRolePermission->record->recordCreate([
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
