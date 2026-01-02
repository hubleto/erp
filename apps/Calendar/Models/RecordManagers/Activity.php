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
    return parent::prepareReadQuery($query, $level)->orderBy('date_start')->orderBy('time_start');
  }

}
