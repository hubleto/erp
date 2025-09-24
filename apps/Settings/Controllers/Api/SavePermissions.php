<?php

namespace Hubleto\App\Community\Settings\Controllers\Api;

use Exception;
use Hubleto\App\Community\Settings\Models\RolePermission;
use Hubleto\App\Community\Auth\Models\UserRole;

class SavePermissions extends \Hubleto\Erp\Controllers\ApiController
{
  public function renderJson(): array
  {
    $roleId = $this->router()->urlParamAsInteger("roleId");
    $rolePermissions = $this->router()->urlParamAsArray("permissions");
    $roleTitle = $this->router()->urlParamAsString("roleTitle");
    $grantAll = $this->router()->urlParamAsBool("grantAll");

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
