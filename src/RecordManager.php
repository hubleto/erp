<?php

namespace Hubleto\Erp;


use Hubleto\App\Community\Auth\Models\User;
use Hubleto\Framework\Helper;

use Hubleto\App\Community\Auth\Models\UserRole;

class RecordManager extends \Hubleto\Framework\RecordManager
{

  public function getPermissions(array $record): array
  {

    $permissions = parent::getPermissions($record);

    // prepare some variables

    $hubleto = \Hubleto\Framework\Loader::getGlobalApp();
    $authProvider = $hubleto->getService(\Hubleto\Framework\AuthProvider::class);
    $idUser = $authProvider->getUserId();

    $hasIdOwner = isset($record['id_owner']);
    $hasIdManager = isset($record['id_manager']);
    $hasIdTeam = isset($record['id_team']);

    $isOwner = false;
    if ($hasIdOwner) {
      $isOwner = $record['id_owner'] == $idUser;
    }

    $isManager = false;
    if ($hasIdManager) {
      $isManager = $record['id_manager'] == $idUser;
    }

    $isTeamMember = false;

    // enable permissions by certain criteria
    $canRead = false;
    $canModify = false;

    if ($authProvider->getUserType() == User::TYPE_ADMINISTRATOR) {
      $canRead = true;
      $canModify = true;
    } if ($authProvider->getUserType() == User::TYPE_CHIEF_OFFICER) {
      // CxO can do anything except for modifying config and settings

      $canRead = true;
      $canModify = true;

      if (str_starts_with($this->model->fullName, 'Hubleto/Core/Config')) {
        $canModify = false;
      }
    } elseif ($authProvider->getUserType() == User::TYPE_MANAGER) {
      // Manager can:
      //   - read only records where he/she is owner or manager
      //   - modify only records where he/she is owner

      $canRead = false;
      $canModify = false;

      if (!$hasIdManager && !$hasIdTeam && !$hasIdOwner) {
        $canRead = true;
        $canModify = true;
      } else {
        if ($hasIdManager && $isManager) {
          $canRead = true;
        }
        if ($hasIdTeam && $isTeamMember) {
          $canRead = true;
        }

        if ($hasIdOwner && $isOwner) {
          $canRead = true;
          $canModify = true;
        }
      }

      $permissions = [$canRead, $canModify, $canModify, $canModify];
    } elseif ($authProvider->getUserType() == User::TYPE_EMPLOYEE) {
      // Employee can:
      //   - read/modify only records where he/she is owner

      if ($hasIdOwner && $isOwner || !$hasIdOwner) {
        $canRead = true;
        $canModify = true;
      }

    } elseif ($authProvider->getUserType() == User::TYPE_ASSISTANT) {
      // Assistant can:
      //   - read/modify only records where he/she is owner

      if ($hasIdOwner && $isOwner || !$hasIdOwner) {
        $canRead = true;
      }
    } elseif ($authProvider->getUserType() == User::TYPE_EXTERNAL) {
      // Externals cannot do anything by default
    }

    $permissions = [true, $canRead, $canModify, $canModify];

    return $permissions;
  }

  public function prepareReadQuery(mixed $query = null, int $level = 0): mixed
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
