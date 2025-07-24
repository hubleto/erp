<?php

namespace HubletoApp\Community\Calendar\Models\RecordManagers;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use HubletoApp\Community\Settings\Models\RecordManagers\User;
use HubletoApp\Community\Settings\Models\RecordManagers\ActivityType;

class Activity extends \Hubleto\Framework\RecordManager
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

  public function prepareReadQuery(mixed $query = null, int $level = 0): mixed
  {
    return parent::prepareReadQuery($query, $level)->orderBy('date_start')->orderBy('time_start');
  }

}
