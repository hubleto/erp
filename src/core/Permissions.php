<?php

namespace HubletoMain\Core;

use HubletoApp\Community\Settings\Models\Permission;
use HubletoApp\Community\Settings\Models\RolePermission;
use HubletoApp\Community\Settings\Models\UserRole;

class Permissions extends \ADIOS\Core\Permissions {

  public \HubletoMain $main;

  protected bool $grantAllPermissions = false;

  function __construct(\HubletoMain $main)
  {
    $this->main = $main;
    parent::__construct($main);
  }

  public function init(): void
  {
    parent::init();
    $this->administratorRoles = $this->loadAdministratorRoles();
  }

  public function createUserRoleModel(): \ADIOS\Core\Model
  {
    return new \HubletoApp\Community\Settings\Models\UserRole($this->app);
  }

  public function DANGEROUS__grantAllPermissions() {
    $this->grantAllPermissions = true;
  }

  public function revokeGrantAllPermissions() {
    $this->grantAllPermissions = false;
  }

  public function loadAdministratorRoles(): array
  {
    if (!isset($this->app->pdo) || !$this->app->pdo->isConnected) return [];
    $mUserRole = new UserRole($this->main);
    $administratorRoles = \ADIOS\Core\Helper::pluck('id', $this->app->pdo->fetchAll("select id from `{$mUserRole->table}` where grant_all = 1"));
    return $administratorRoles;
  }

  /**
  * @return array<int, array<int, string>>
  */
  public function loadPermissions(): array
  {
    $permissions = parent::loadPermissions();

    if (isset($this->app->pdo) && $this->app->pdo->isConnected) {
      $mUserRole = new UserRole($this->main);

      $idCommonUserRoles = \ADIOS\Core\Helper::pluck('id', $this->app->pdo->fetchAll("select id from `{$mUserRole->table}` where grant_all = 0"));

      foreach ($idCommonUserRoles as $idCommonRole) {
        $idCommonRole = (int) $idCommonRole;

        $mRolePermission = new RolePermission($this->main);

        /** @var array<int, array> */
        $rolePermissions = (array) $mRolePermission->record
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

  public function granted(string $permission, array $userRoles = []) : bool
  {
    if ($this->grantAllPermissions) {
      return true;
    } else {
      return parent::granted($permission, $userRoles);
    }
  }

  public function isAppPermittedForActiveUser(\HubletoMain\Core\App $app) {
    $userRoles = $this->app->auth->getUserRoles();

    if (
      $this->grantAllPermissions
      || $app->permittedForAllUsers
      || count(array_intersect($this->administratorRoles, $userRoles)) > 0
    ) return true;

    $user = $this->main->auth->getUser();
    $userApps = @json_decode($user['apps'], true);

    return is_array($userApps) && in_array($app->namespace, $userApps);
  }
}
