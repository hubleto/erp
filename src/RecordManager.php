<?php

namespace HubletoMain;

use Hubleto\Framework\Helper;
use HubletoApp\Community\Settings\Models\User;
use HubletoApp\Community\Settings\Models\UserRole;

class RecordManager extends \Hubleto\Framework\RecordManager
{

  public function getPermissions(array $record): array
  {

    $permissions = parent::getPermissions($record);

    // prepare some variables

    $main = \Hubleto\Framework\Loader::getGlobalApp();
    $idUser = $main->auth->getUserId();
// var_dump($main->auth->getUserType());
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
    // if ($hasIdTeam) {
    //   $isTeamMember = $main->auth->isUserMemberOfTeam($record['id_team']);
    // }

    // enable permissions by certain criteria
    $canRead = false;
    $canModify = false;

    if ($main->auth->getUserType() == User::TYPE_ADMINISTRATOR) {
      $canRead = true;
      $canModify = true;
    } if ($main->auth->getUserType() == User::TYPE_CHIEF_OFFICER) {
      // CxO can do anything except for modifying config and settings

      $canRead = true;
      $canModify = true;

      if (str_starts_with($this->model->fullName, 'Hubleto/Core/Config')) {
        $canModify = false;
      }
    } elseif ($main->auth->getUserType() == User::TYPE_MANAGER) {
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
    } elseif ($main->auth->getUserType() == User::TYPE_EMPLOYEE) {
      // Employee can:
      //   - read/modify only records where he/she is owner

      if ($hasIdOwner && $isOwner || !$hasIdOwner) {
        $canRead = true;
        $canModify = true;
      }

    } elseif ($main->auth->getUserType() == User::TYPE_ASSISTANT) {
      // Assistant can:
      //   - read/modify only records where he/she is owner

      if ($hasIdOwner && $isOwner || !$hasIdOwner) {
        $canRead = true;
      }
    } elseif ($main->auth->getUserType() == User::TYPE_EXTERNAL) {
      // Externals cannot do anything by default
    }

    $permissions = [true, $canRead, $canModify, $canModify];

    // merge default permissions with user configured

    // $permissions = [
    //   $permissions[0] || $main->permissions->granted($this->model->permission . ':Create'),
    //   $permissions[1] || $main->permissions->granted($this->model->permission . ':Read'),
    //   $permissions[2] || $main->permissions->granted($this->model->permission . ':Update'),
    //   $permissions[3] || $main->permissions->granted($this->model->permission . ':Delete'),
    // ];

    return $permissions;
  }

  public function prepareReadQuery(mixed $query = null, int $level = 0): mixed
  {
    $main = \Hubleto\Framework\Loader::getGlobalApp();

    $query = parent::prepareReadQuery($query, $level);

    $hasIdOwner = $this->model->hasColumn('id_owner');
    $hasIdManager = $this->model->hasColumn('id_manager');
    $hasIdTeam = $this->model->hasColumn('id_team');

    $idUser = $main->auth->getUserId();

    $user = $main->auth->getUser();
    $userTeams = [];
    foreach ($user['TEAMS'] ?? [] as $team) {
      $userTeams[] = $team['id'] ?? 0;
    }

    if ($main->auth->getUserType() == User::TYPE_MANAGER) {
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
    } elseif ($main->auth->getUserType() == User::TYPE_EMPLOYEE && $hasIdOwner) {
      $query = $query->where($this->table . '.id_owner', $idUser);
    } elseif ($main->auth->getUserType() == User::TYPE_ASSISTANT && $hasIdOwner) {
      $query = $query->where($this->table . '.id_owner', $idUser);
    }

    // junctions

    $junctionModel = $main->getRouter()->urlParamAsString('junctionModel');
    $junctionSourceColumn = $main->getRouter()->urlParamAsString('junctionSourceColumn');
    $junctionDestinationColumn = $main->getRouter()->urlParamAsString('junctionDestinationColumn');
    $junctionSourceRecordId = $main->getRouter()->urlParamAsInteger('junctionSourceRecordId');

    if (!empty($junctionModel) && !empty($junctionSourceColumn) && $junctionSourceRecordId > 0) {
      $junctionModelObj = $main->load($junctionModel);
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
