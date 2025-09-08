<?php

namespace Hubleto\App\Community\Calendar\Models\RecordManagers;

use Hubleto\App\Community\Settings\Models\RecordManagers\ActivityType;
use Hubleto\App\Community\Settings\Models\RecordManagers\User;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SharedCalendar extends \Hubleto\Erp\RecordManager
{
  public $table = 'shared_calendars';

  /** @return BelongsTo<User, covariant Customer> */
  public function OWNER(): BelongsTo
  {
    return $this->belongsTo(User::class, 'id_owner', 'id');
  }

}
