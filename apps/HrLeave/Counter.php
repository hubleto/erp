<?php

namespace Hubleto\App\Community\HrLeave;

use Hubleto\Erp\Core;

class Counter extends Core
{
  public function pendingRequests(): int
  {
    $mRequest = $this->getModel(Models\LeaveRequest::class);

    return $mRequest->record->prepareReadQuery()
      ->whereHas('WORKFLOW_STEP', function ($query) {
        $query->whereIn('tag', [
          'hr-leave-submitted',
          'hr-leave-manager-review',
          'hr-leave-hr-review',
        ]);
      })
      ->count();
  }
}