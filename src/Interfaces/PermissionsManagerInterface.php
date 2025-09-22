<?php

namespace Hubleto\Erp\Interfaces;

interface PermissionsManagerInterface
{

  public function init(): void;
  public function createUserRoleModel(): null|\Hubleto\Framework\Model;
  public function DANGEROUS__grantAllPermissions(): void;
  public function revokeGrantAllPermissions(): void;
  public function loadAdministratorRoles(): array;
  public function loadAdministratorTypes(): array;
  public function expandPermissionGroups(): void;
  public function set(string $permission, int $idUserRole, bool $isEnabled);
  public function hasRole(int|string $role): bool;
  public function granted(string $permission, array $userRoles = [], int $userType = 0): bool;
  public function check(string $permission): void;
  public function loadPermissions(): array;
  public function isAppPermittedForActiveUser(\Hubleto\Framework\Interfaces\AppInterface $app): bool;

}
