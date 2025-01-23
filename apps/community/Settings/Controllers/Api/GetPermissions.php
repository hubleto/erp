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
    $roleId = (int) $this->main->params["roleId"] ?? null;

    try {
      $mPermission = new Permission($this->main);
      $allPermissions = $mPermission->eloquent->orderBy("permission", "asc")->get()->toArray();

      foreach ($allPermissions as $permission) {

        //capture the namespace of the app
        $pattern = "#^((?:[^/]+/){2}[^/]+)#";
        preg_match($pattern, $permission["permission"], $matches);
        $appNamespace = $matches[1];

        //capture the Model, Controller or Api (fourth segement of the namespace)
        $pattern = "#^(?:[^/]+/){3}([^/]+)#";
        preg_match($pattern, $permission["permission"], $matches);
        $MVCNamespace = $matches[1];

        if (in_array($MVCNamespace, $this->MVCNamespaces)) {
          $sortedAllPermissions[$appNamespace][$MVCNamespace][] = $permission;
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
