<?php

namespace Hubleto\App\Community\HrLeave\Models\RecordManagers;

use Hubleto\App\Community\Auth\Models\RecordManagers\User;
use Hubleto\App\Community\HrLeave\Models\RecordManagers\LeaveType;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LeaveBalance extends \Hubleto\Erp\RecordManager
{
  public $table = 'hr_leave_balances';

  public function USER(): BelongsTo
  {
    return $this->belongsTo(User::class, 'id_user', 'id');
  }

  public function LEAVE_TYPE(): BelongsTo
  {
    return $this->belongsTo(LeaveType::class, 'id_leave_type', 'id');
  }
}