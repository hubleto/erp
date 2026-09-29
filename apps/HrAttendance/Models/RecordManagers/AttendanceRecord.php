<?php

namespace Hubleto\App\Community\HrAttendance\Models\RecordManagers;

use Hubleto\App\Community\Auth\Models\RecordManagers\User;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AttendanceRecord extends \Hubleto\Erp\RecordManager
{
  public $table = 'hr_attendance_records';

  public function USER(): BelongsTo
  {
    return $this->belongsTo(User::class, 'id_user', 'id');
  }
}