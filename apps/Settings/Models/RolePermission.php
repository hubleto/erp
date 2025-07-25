<?php

namespace HubletoApp\Community\Settings\Models;

use Hubleto\Framework\Db\Column\Lookup;

class RolePermission extends \Hubleto\Framework\Models\Model
{
  public string $table = 'role_permissions';
  public string $recordManagerClass = RecordManagers\RolePermission::class;

  public array $relations = [
    'ROLE' => [ self::BELONGS_TO, UserRole::class, 'id_role', 'id' ],
    'PERMISSION' => [ self::BELONGS_TO, Permission::class, 'id_permission', 'id' ],
  ];

  public function describeColumns(): array
  {
    return array_merge(parent::describeColumns(), [
      'id_permission' => (new Lookup($this, $this->translate('Permission'), Permission::class))->setRequired(),
      'id_role' => (new Lookup($this, $this->translate('Role'), UserRole::class))->setRequired(),
    ]);
  }

  public function describeTable(): \Hubleto\Framework\Description\Table
  {
    $description = parent::describeTable();

    $description->ui['title'] = 'Role Permissions';
    $description->ui['showHeader'] = true;
    $description->ui['showFulltextSearch'] = true;
    $description->ui['showFooter'] = false;

    return $description;
  }

  public function findPermissionByString(string $permission): array
  {
    $mPermission = $this->main->di->create(Permission::class);
    $pData = $mPermission->record->where('permission', $permission)->first()?->toArray();

    if (!is_array($pData)) {
      $mPermission = $this->main->di->create(Permission::class);
      $mPermission->record->recordCreate(['permission' => $permission]);

      $pData = $mPermission->record->where('permission', $permission)->first()?->toArray();
    }

    return is_array($pData) ? $pData : [];
  }

  public function grantPermissionByString(int $idRole, string $permission): void
  {
    $idPermission = $this->findPermissionByString($permission)['id'] ?? 0;
    $this->grantPermissionById($idRole, $idPermission);
  }

  public function denyPermissionByString(int $idRole, string $permission): void
  {
    $idPermission = $this->findPermissionByString($permission)['id'] ?? 0;
    $this->record->where('id_permission', $idPermission)->where('id_role', $idRole)->delete();
  }

  public function grantPermissionById(int $idRole, int $idPermission): void
  {
    if (
      $idPermission > 0
      && $this->record->where('id_permission', $idPermission)->where('id_role', $idRole)->count() == 0
    ) {
      $this->record->recordCreate(['id_permission' => $idPermission, 'id_role' => $idRole]);
    }
  }

  public function grantPermissionsLike(int $idRole, string $permission): void
  {
    $mPermission = $this->main->di->create(Permission::class);
    $permissions = $mPermission->record->where('permission', 'like', $permission)->get()?->toArray();

    foreach ($permissions as $prm) {
      $this->grantPermissionById($idRole, $prm['id'] ?? 0);
    }
  }


  public function grantPermissionsForModel(
    int $idRole,
    string $modelPermission,
    array $permissions // example: [true, true, true, true]
  ): void {
    if ($permissions[0]) {
      $this->grantPermissionByString($idRole, $modelPermission . ':Create');
    }
    if ($permissions[1]) {
      $this->grantPermissionByString($idRole, $modelPermission . ':Read');
    }
    if ($permissions[2]) {
      $this->grantPermissionByString($idRole, $modelPermission . ':Update');
    }
    if ($permissions[3]) {
      $this->grantPermissionByString($idRole, $modelPermission . ':Delete');
    }
  }

}
