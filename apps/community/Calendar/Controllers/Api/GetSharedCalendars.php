<?php

namespace HubletoApp\Community\Calendar\Controllers\Api;


use ADIOS\Core\Controller;
use HubletoApp\Community\Calendar\Models\RecordManagers\SharedCalendar;

class GetSharedCalendars extends \HubletoMain\Core\Controllers\ApiController
{

  public function renderJson(): ?array
  {
    $mSharedCalendar = new SharedCalendar();
    return $mSharedCalendar->get(['calendar', 'share_key'])->toArray();
  }

}