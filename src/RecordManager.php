<?php

namespace Hubleto\Erp;


use Hubleto\App\Community\Auth\Models\User;

class RecordManager extends \Hubleto\Framework\RecordManager
{

  // False only while prepareReadQueryOfAllUsers() runs. A flag, not a parameter, so the apps'
  // prepareReadQuery() overrides need no change: they call the parent, which reads the flag.
  protected bool $filterByActiveUser = true;

  /**
   * Same as prepareReadQuery(), but the records are not limited to the ones the active user owns or manages.
   * For places where a setting permits a user to see records of other users, e.g. calendars of other users.
   */
  public function prepareReadQueryOfAllUsers(mixed $query = null, int $level = 0, array|null $includeRelations = null): mixed
  {
    $this->filterByActiveUser = false;
    try {
      $query = $this->prepareReadQuery($query, $level, $includeRelations);
    } finally {
      $this->filterByActiveUser = true;
    }
    return $query;
  }

  // Records with an "attendees" column (JSON list of user ids, e.g. activities) are visible to their attendees too.
  private function orWhereUserAttends(mixed $query, mixed $idUser): void
  {
    $idUserAsText = (string) (int) $idUser;
    $attendeesJson = "coalesce(nullif({$this->table}.attendees, ''), '[]')";
    // The ids may be stored as numbers or as strings.
    $query->orWhereRaw("JSON_CONTAINS({$attendeesJson}, ?)", [$idUserAsText]);
    $query->orWhereRaw("JSON_CONTAINS({$attendeesJson}, ?)", [json_encode($idUserAsText)]);
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

    $query = parent::prepareReadQuery($query, $level, $includeRelations);

    $hasIdOwner = $this->model->hasColumn('id_owner');
    $hasIdManager = $this->model->hasColumn('id_manager');
    $hasIdTeam = $this->model->hasColumn('id_team');
    $hasSharedWith = $this->model->hasColumn('shared_with');
    $hasAttendees = $this->model->hasColumn('attendees');

    $authProvider = $hubleto->authProvider();

    $idUser = $authProvider->getUserId();

    $user = $authProvider->getUser();
    $userTeams = [];
    foreach ($user['TEAMS'] ?? [] as $team) {
      $userTeams[] = $team['id'] ?? 0;
    }

    if (
      $this->filterByActiveUser
      && (
        $authProvider->getUserType() == User::TYPE_MANAGER
        || $authProvider->getUserType() == User::TYPE_CHIEF_OFFICER
      )
    ) {
      if ($hasIdOwner && $hasIdManager && $hasIdTeam) {
        $query = $query->where(function ($q) use ($idUser, $userTeams, $hasAttendees) {
          $q
            ->where($this->table . '.id_owner', $idUser)
            ->orWhere($this->table . '.id_manager', $idUser)
            ->orWhereIn($this->table . '.id_team', $userTeams)
          ;
          if ($hasAttendees) $this->orWhereUserAttends($q, $idUser);
        });
      } elseif ($hasIdOwner && $hasIdManager) {
        $query = $query->where(function ($q) use ($idUser, $hasSharedWith, $hasAttendees) {
          $q
            ->where($this->table . '.id_owner', $idUser)
            ->orWhere($this->table . '.id_manager', $idUser)
          ;
          if ($hasSharedWith) {
            $q->orWhereRaw("JSON_CONTAINS_PATH({$this->table}.shared_with, 'one', '$.\"{$idUser}\"')");
          }
          if ($hasAttendees) $this->orWhereUserAttends($q, $idUser);
        });
      } elseif ($hasIdOwner) {
        $query = $query->where(function($q) use ($idUser, $hasSharedWith, $hasAttendees) {
          $q->where($this->table . '.id_owner', $idUser);
          if ($hasSharedWith) {
            $q->orWhereRaw("JSON_CONTAINS_PATH({$this->table}.shared_with, 'one', '$.\"{$idUser}\"')");
          }
          if ($hasAttendees) $this->orWhereUserAttends($q, $idUser);
        });
      } elseif ($hasIdManager) {
        $query = $query->where(function($q) use ($idUser, $hasSharedWith, $hasAttendees) {
          $q->where($this->table . '.id_manager', $idUser);
          if ($hasSharedWith) {
            $q->orWhereRaw("JSON_CONTAINS_PATH({$this->table}.shared_with, 'one', '$.\"{$idUser}\"')");
          }
          if ($hasAttendees) $this->orWhereUserAttends($q, $idUser);
        });
      } elseif ($hasIdTeam) {
        $query = $query->where(function($q) use ($idUser, $hasSharedWith, $hasAttendees) {
          $q->where($this->table . '.id_team', $idUser);
          if ($hasSharedWith) {
            $q->orWhereRaw("JSON_CONTAINS_PATH({$this->table}.shared_with, 'one', '$.\"{$idUser}\"')");
          }
          if ($hasAttendees) $this->orWhereUserAttends($q, $idUser);
        });
      }
    } elseif (
      $this->filterByActiveUser
      && ($authProvider->getUserType() == User::TYPE_EMPLOYEE || $authProvider->getUserType() == User::TYPE_ASSISTANT)
      && $hasIdOwner
    ) {
      $query = $query->where(function($q) use ($idUser, $hasSharedWith, $hasAttendees) {
        $q->where($this->table . '.id_owner', $idUser);
        if ($hasSharedWith) {
          $q->orWhereRaw("JSON_CONTAINS_PATH({$this->table}.shared_with, 'one', '$.\"{$idUser}\"')");
        }
        if ($hasAttendees) $this->orWhereUserAttends($q, $idUser);
      });
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
