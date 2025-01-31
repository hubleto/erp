<?php

namespace HubletoApp\Community\Settings\Controllers\Api;

use Exception;
use HubletoApp\Community\Settings\Models\Permission;
use HubletoApp\Community\Settings\Models\RolePermission;

class GetPermissions extends \HubletoMain\Core\Controller
{
  private $MVCNamespaces = [
    "Models",
    "Controllers",
    "Api"
  ];

  public function renderJson(): ?array
  {
    $allPermissions = [];
    $sortedAllPermissions = [];
    $rolePermissions = [];
    $roleId = $this->main->urlParamAsInteger("roleId") ?? null;

    try {
      $mPermission = new Permission($this->main);
      $allPermissions = $mPermission->eloquent->orderBy("permission", "asc")->get()->toArray();

      foreach ($allPermissions as $permission) {
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
        $explodedStrings = explode("/", $permission["permission"]);

        //capture the namespace of the app
        if (isset($explodedStrings[2])) $appNamespace = $explodedStrings[2];

        //capture the Model, Controller or Api
        if (isset($explodedStrings[3])) $MVCNamespace = $explodedStrings[3];

        //capture the namespace after the MVC namespaces
        $modPermission = $permission;
        if (isset($explodedStrings[4])) $modPermission["alias"] = $explodedStrings[4];

        if (in_array($MVCNamespace, $this->MVCNamespaces)) {
          $sortedAllPermissions[$appNamespace][$MVCNamespace][] = $modPermission;
        } else $sortedAllPermissions[$appNamespace]["Other"][] = $permission;
      }

      if ($roleId && $roleId > 0) {
        $mRolePermission = new RolePermission($this->main);
        $rolePermissions = $mRolePermission->eloquent->where("id_role", $roleId)->pluck("id_permission")->toArray();
      } else $rolePermissions = [];
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
