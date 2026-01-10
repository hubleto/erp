<?php

namespace Hubleto\Erp;

use Hubleto\Framework\Helper;
use Hubleto\App\Community\Auth\AuthProvider;

/**
 * Core implementation of model.
 */
class Model extends \Hubleto\Framework\Model
{

  const COLUMN_ID_CUSTOMER_DEFAULT_ICON = 'fas fa-address-card bg-yellow-50 rounded text-yellow-600 p-2 mr-2 w-10 text-center block';
  const COLUMN_ID_SUPPLIER_DEFAULT_ICON = 'fas fa-truck bg-lime-50 rounded text-lime-600 p-2 mr-2 w-10 text-center block';
  const COLUMN_CONTACT_DEFAULT_ICON = 'fas fa-id-badge bg-yellow-50 rounded text-yellow-600 p-2 mr-2 w-10 text-center block';
  const COLUMN_IDENTIFIER_DEFUALT_ICON = 'fas fa-pen bg-blue-50 rounded text-blue-600 p-2 mr-2 w-10 text-center block';
  const COLUMN_NAME_DEFAULT_ICON = 'fas fa-a bg-sky-50 rounded text-sky-600 p-2 mr-2 w-10 text-center block';
  const COLUMN_ADDRESS_DEFAULT_ICON = 'fas fa-location-dot bg-green-50 rounded text-green-600 p-2 mr-2 w-10 text-center block';
  const COLUMN_COLOR_DEFAULT_ICON = 'fas fa-palette bg-violet-50 rounded text-violet-600 p-2 mr-2 w-10 text-center block';

  /**
   * [Description for getPermissions]
   *
   * @param array $record
   * 
   * @return array
   * 
   */
  public function getPermissions(array $record): array
  {

    $permissions = parent::getPermissions($record);

    // prepare some variables

    $hubleto = \Hubleto\Framework\Loader::getGlobalApp();

    /** @var AuthProvider */
    $authProvider = $hubleto->getService(AuthProvider::class);
    $idUser = $authProvider->getUserId();
    $permissions = $authProvider->getUserPermissions();

    $hasIdOwner = isset($record['id_owner']);
    $hasIdManager = isset($record['id_manager']);
    $hasIdTeam = isset($record['id_team']);

    $idOwner = $record['id_owner'] ?? 0;
    $idManager = $record['id_manager'] ?? 0;
    $idTeam = $record['id_team'] ?? 0;
    $sharedWith = @json_decode($record['shared_with'] ?? '', true);

    if (!is_array($sharedWith)) $sharedWith = [];

    $canRead = false;
    $canModify = false;

    $isOwner = $hasIdOwner ? $idOwner > 0 && $idOwner == $idUser : true;
    $isManager = $hasIdManager ? $idManager > 0 && $idManager == $idUser : true;
    $isTeamMember = $hasIdTeam ? $idTeam > 0 && $authProvider->isTeamMember($idTeam) : true;

    // enable permissions by certain criteria

    if ($permissions['recordsRead'] == 'owned') {
      $canRead = $isOwner;
    } elseif ($permissions['recordsRead'] == 'owned-and-managed') {
      $canRead = $isOwner || $isManager;
    } else if ($permissions['recordsRead'] == 'owned-managed-and-team') {
      $canRead = $isOwner || $isManager || $isTeamMember;
    } else {
      $canRead = true;
    }

    if ($permissions['recordsModify'] == 'owned') {
      $canModify = $isOwner;
    } elseif ($permissions['recordsModify'] == 'owned-and-managed') {
      $canModify = $isOwner || $isManager;
    } else if ($permissions['recordsModify'] == 'owned-managed-and-team') {
      $canModify = $isOwner || $isManager || $isTeamMember;
    } else {
      $canModify = true;
    }

    if (isset($sharedWith[$idUser])) {
      if ($sharedWith[$idUser] == 'read' || $sharedWith[$idUser] == 'modify') $canRead = true;
      if ($sharedWith[$idUser] == 'modify') $canModify = true;
    }

    $permissions = [true, $canRead, $canModify, $canModify];

    return $permissions;
  }

}
