<?php

namespace Hubleto\App\Community\HrAttendance;

use Hubleto\Erp\Core;

class Counter extends Core
{
  public function unapprovedRecords(): int
  {
    $mRecord = $this->getModel(Models\AttendanceRecord::class);

    return $mRecord->record->prepareReadQuery()
      ->where($mRecord->table . '.is_approved', false)
      ->count();
  }
}