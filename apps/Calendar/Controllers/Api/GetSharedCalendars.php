<?php

namespace Hubleto\App\Community\Calendar\Controllers\Api;

use Hubleto\App\Community\Calendar\Models\RecordManagers\SharedCalendar;

class GetSharedCalendars extends \Hubleto\Erp\Controllers\ApiController
{
  public function renderJson(): array
  {
    $mSharedCalendar = new SharedCalendar();
    return $mSharedCalendar->get(['calendar', 'share_key'])->toArray();
  }

}
