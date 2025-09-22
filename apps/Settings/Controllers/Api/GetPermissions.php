<?php

namespace Hubleto\App\Community\Settings\Controllers\Api;

use Exception;
use Hubleto\App\Community\Settings\Models\Permission;
use Hubleto\App\Community\Settings\Models\RolePermission;

class GetPermissions extends \Hubleto\Erp\Controllers\ApiController
{
  private array $MVCNamespaces = [
    "Models",
    "Controllers",
    "Api"
  ];

  public function renderJson(): ?array
  {
    $allPermissions = [];
    $sortedAllPermissions = [];
    $rolePermissions = [];
    $roleId = $this->router()->urlParamAsInteger("roleId");

    try {

      /** @var Permission */
      $mPermission = $this->getModel(Permission::class);

      $allPermissions = $mPermission->record->orderBy("permission", "asc")->get()->toArray();

      foreach ($allPermissions as $permission) { //@phpstan-ignore-line
        /*
          [0] => HubletoApp namespace
          [1] => App Version
          [2] => App Name
          [3] => Controller, Model, Api or other
          [4] => Permission name
        */

        $appNamespace = "";
        $MVCNamespace = "";
        $modPermission = [];
        $explodedStrings = explode("/", (string) $permission["permission"]);

        //capture the namespace of the app
        if (isset($explodedStrings[2])) {
          $appNamespace = $explodedStrings[2];
        }

        //capture the Model, Controller or Api
        if (isset($explodedStrings[3])) {
          $MVCNamespace = $explodedStrings[3];
        }

        //capture the namespace after the MVC namespaces
        $modPermission = $permission;
        if (isset($explodedStrings[4])) {
          $modPermission["alias"] = $explodedStrings[4];
        }

        if (in_array($MVCNamespace, $this->MVCNamespaces)) {
          $sortedAllPermissions[$appNamespace][$MVCNamespace][] = $modPermission;
        } else {
          $sortedAllPermissions[$appNamespace]["Other"][] = $permission;
        }
      }

      if ($roleId > 0) {
        $mRolePermission = $this->getService(RolePermission::class);
        $rolePermissions = $mRolePermission->record->where("id_role", $roleId)->pluck("id_permission")->toArray();
      } else {
        $rolePermissions = [];
      }
    } catch (Exception $e) {
      return [
        "status" => "failed",
        "error" => $e
      ];
    }

    return [
      "status" => "success",
      "sortedAllPermissions" => $sortedAllPermissions,
      "rolePermissions" => $rolePermissions
    ];
  }
}
