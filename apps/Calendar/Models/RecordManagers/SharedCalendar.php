<?php

namespace HubletoApp\Community\Calendar\Models\RecordManagers;

use HubletoApp\Community\Settings\Models\RecordManagers\ActivityType;
use HubletoApp\Community\Settings\Models\RecordManagers\User;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SharedCalendar extends \HubletoMain\RecordManager
{
  public $table = 'shared_calendars';

  /** @return BelongsTo<User, covariant Customer> */
  public function OWNER(): BelongsTo
  {
    return $this->belongsTo(User::class, 'id_owner', 'id');
  }

}
