<?php

namespace Hubleto\App\Community\Calendar\Models\RecordManagers;

use Hubleto\App\Community\Auth\Models\RecordManagers\User;
use Hubleto\App\Community\Settings\Models\RecordManagers\ActivityType;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Activity extends \Hubleto\Erp\RecordManager
{
  public $table = 'activities';

  /** @return BelongsTo<User, covariant Customer> */
  public function OWNER(): BelongsTo
  {
    return $this->belongsTo(User::class, 'id_owner', 'id');
  }

  /** @return BelongsTo<User, covariant Customer> */
  public function ACTIVITY_TYPE(): BelongsTo
  {
    return $this->belongsTo(ActivityType::class, 'id_activity_type', 'id');
  }

  public function prepareReadQuery(mixed $query = null, int $level = 0, array|null $includeRelations = null): mixed
  {
    // The parent would limit the activities to the owned ones. It is turned off here, because
    // the limit below also lets the attendees read the activity, and everyone an activity for everyone.
    $limitToActiveUser = $this->filterByActiveUser;
    $this->filterByActiveUser = false;
    try {
      $query = parent::prepareReadQuery($query, $level, $includeRelations);
    } finally {
      $this->filterByActiveUser = $limitToActiveUser;
    }

    // Activities have only an owner, so both limits of the parent mean "owned activities".
    $isActiveUserLimited = $this->isActiveUserLimitedToOwnManagedAndTeamRecords() || $this->isActiveUserLimitedToOwnRecords();
    if ($limitToActiveUser && $isActiveUserLimited) {
      $authProvider = \Hubleto\Framework\Loader::getGlobalApp()->authProvider();
      $query = $this->whereActivityIsForUser($query, (int) $authProvider->getUserId());
    }

    return $query->orderBy('date_start')->orderBy('time_start');
  }

  // Activities the user owns or attends, and activities for everyone.
  public function whereActivityIsForUser(mixed $query, int $idUser): mixed
  {
    return $query->where(function ($activitiesOfUser) use ($idUser) {
      $activitiesOfUser->where($this->table . '.id_owner', $idUser);
      $activitiesOfUser->orWhere(function ($attendedActivities) use ($idUser) {
        $this->whereUserIdListContains($attendedActivities, 'attendees', $idUser);
      });
      $activitiesOfUser->orWhere($this->table . '.for_everyone', 1);
    });
  }

}
