<?php

namespace HubletoApp\Community\Settings\Controllers\Api;

use Exception;
use HubletoApp\Community\Settings\Models\RolePermission;
use HubletoApp\Community\Settings\Models\UserRole;

class SavePermissions extends \HubletoMain\Controllers\ApiController
{
  public function renderJson(): ?array
  {
    $roleId = $this->main->urlParamAsInteger("roleId");
    $rolePermissions = $this->main->urlParamAsArray("permissions");
    $roleTitle = $this->main->urlParamAsString("roleTitle");
    $grantAll = $this->main->urlParamAsBool("grantAll");

    if ($roleId > 0) {
      try {
        $mUserRole = $this->main->di->create(UserRole::class);
        $userRole = $mUserRole->record->find($roleId);
        $userRole->update([
          "role" => $roleTitle,
          "grant_all" => $grantAll
        ]);

        $mRolePermission = $this->main->di->create(RolePermission::class);
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
