<?php

namespace Hubleto\App\Community\HrLeave;

use Hubleto\Erp\Core;

class Counter extends Core
{
  public function pendingRequests(): int
  {
    $mRequest = $this->getModel(Models\LeaveRequest::class);

    return $mRequest->record->prepareReadQuery()
      ->where($mRequest->table . '.status', $this->translate('Pending'))
      ->count();
  }
}