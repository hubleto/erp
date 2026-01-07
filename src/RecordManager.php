<?php

namespace Hubleto\Erp;


use Hubleto\App\Community\Auth\Models\User;
use Hubleto\Framework\Helper;
use Hubleto\App\Community\Auth\AuthProvider;

use Hubleto\App\Community\Auth\Models\UserRole;

class RecordManager extends \Hubleto\Framework\RecordManager
{

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

  /**
   * [Description for prepareReadQuery]
   *
   * @param mixed|null $query
   * @param int $level
   * @param array|null|null $includeRelations
   * 
   * @return mixed
   * 
   */
  public function prepareReadQuery(mixed $query = null, int $level = 0, array|null $includeRelations = null): mixed
  {
    $hubleto = \Hubleto\Framework\Loader::getGlobalApp();

    $query = parent::prepareReadQuery($query, $level);

    $hasIdOwner = $this->model->hasColumn('id_owner');
    $hasIdManager = $this->model->hasColumn('id_manager');
    $hasIdTeam = $this->model->hasColumn('id_team');

    $authProvider = $hubleto->getService(\Hubleto\Framework\AuthProvider::class);

    $idUser = $authProvider->getUserId();

    $user = $authProvider->getUser();
    $userTeams = [];
    foreach ($user['TEAMS'] ?? [] as $team) {
      $userTeams[] = $team['id'] ?? 0;
    }

    if ($authProvider->getUserType() == User::TYPE_MANAGER) {
      if ($hasIdOwner && $hasIdManager && $hasIdTeam) {
        $query = $query->where(function ($q) use ($idUser, $userTeams) {
          $q
            ->where($this->table . '.id_owner', $idUser)
            ->orWhere($this->table . '.id_manager', $idUser)
            ->orWhereIn($this->table . '.id_team', $userTeams)
          ;
        });
      } elseif ($hasIdOwner && $hasIdManager) {
        $query = $query->where(function ($q) use ($idUser) {
          $q
            ->where($this->table . '.id_owner', $idUser)
            ->orWhere($this->table . '.id_manager', $idUser);
        });
      } elseif ($hasIdOwner) {
        $query = $query->where($this->table . '.id_owner', $idUser);
      } elseif ($hasIdManager) {
        $query = $query->where($this->table . '.id_manager', $idUser);
      } elseif ($hasIdTeam) {
        $query = $query->whereIn($this->table . '.id_team', $userTeams);
      }
    } elseif ($authProvider->getUserType() == User::TYPE_EMPLOYEE && $hasIdOwner) {
      $query = $query->where($this->table . '.id_owner', $idUser);
    } elseif ($authProvider->getUserType() == User::TYPE_ASSISTANT && $hasIdOwner) {
      $query = $query->where($this->table . '.id_owner', $idUser);
    }

    // junctions

    $junctionModel = $hubleto->router()->urlParamAsString('junctionModel');
    $junctionSourceColumn = $hubleto->router()->urlParamAsString('junctionSourceColumn');
    $junctionDestinationColumn = $hubleto->router()->urlParamAsString('junctionDestinationColumn');
    $junctionSourceRecordId = $hubleto->router()->urlParamAsInteger('junctionSourceRecordId');

    if (!empty($junctionModel) && !empty($junctionSourceColumn) && $junctionSourceRecordId > 0) {
      $junctionModelObj = $hubleto->getModel($junctionModel);
      if ($junctionModelObj) {
        $destinationIds = $junctionModelObj->record
          ->where($junctionSourceColumn, $junctionSourceRecordId)
          ->pluck($junctionDestinationColumn)
          ->toArray()
        ;
        $query = $query->whereIn($this->table . '.id', $destinationIds);
      }
    }

    return $query;
  }
}
