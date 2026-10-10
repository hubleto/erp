<?php

namespace Hubleto\App\Community\Settings\Models;

use Hubleto\App\Community\Auth\Models\UserRole;
use Hubleto\Framework\Db\Column\Lookup;

class RolePermission extends \Hubleto\Erp\Model
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
    $description->ui['addButtonText'] = $this->translate('Assign permission to role');
    $description->show(['header', 'fulltextSearch', 'columnSearch', 'moreActionsButton']);
    $description->hide(['footer']);
    return $description;
  }

  public function findPermissionByString(string $permission): array
  {
    /** @var Permission */
    $mPermission = $this->getModel(Permission::class);

    $pData = $mPermission->record->where('permission', $permission)->first()?->toArray();

    if (!is_array($pData)) {
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

  /** Resolve permissions and existing grants once for the whole assignment. */
  public function grantPermissionsByString(array $idRoles, array $permissions): void
  {
    if ($this->db() instanceof \Hubleto\Framework\Services\Db && $this->db()->isFreshInstallation()) {
      $this->assignPermissionsByString($idRoles, $permissions);
    } else {
      $this->record->getConnection()->transaction(fn() => $this->assignPermissionsByString($idRoles, $permissions));
    }
  }

  private function assignPermissionsByString(array $idRoles, array $permissions): void
  {
    $idRoles = array_values(array_unique(array_map('intval', $idRoles)));
    $permissions = array_values(array_unique($permissions));
    if (!$idRoles || !$permissions) return;

    $mPermission = $this->getModel(Permission::class);
    $ids = [];
    foreach ($mPermission->record->whereIn('permission', $permissions)->orderBy('id')->get()->toArray() as $row) {
      $ids[$row['permission']] ??= (int) $row['id'];
    }
    foreach ($permissions as $permission) {
      if (!isset($ids[$permission])) $ids[$permission] = (int) ($this->findPermissionByString($permission)['id'] ?? 0);
    }

    $granted = [];
    foreach ($this->record->whereIn('id_role', $idRoles)->whereIn('id_permission', array_values($ids))->get()->toArray() as $row) {
      $granted[$row['id_role'] . '/' . $row['id_permission']] = true;
    }
    foreach ($idRoles as $idRole) {
      foreach ($ids as $idPermission) {
        if ($idPermission > 0 && !isset($granted[$idRole . '/' . $idPermission])) {
          $this->record->recordCreate(['id_permission' => $idPermission, 'id_role' => $idRole]);
          $granted[$idRole . '/' . $idPermission] = true;
        }
      }
    }
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
    /** @var Permission */
    $mPermission = $this->getModel(Permission::class);

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
