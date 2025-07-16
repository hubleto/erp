<?php

namespace HubletoApp\Community\Calendar\Controllers\Api;


use ADIOS\Core\Controller;
use HubletoApp\Community\Calendar\Models\RecordManagers\SharedCalendar;

class ShareCalendar extends \HubletoMain\Core\Controllers\ApiController
{

  public function renderJson(): ?array
  {
    if (!isset($this->app->getUrlParams()['calendar'])) {
      return [];
    }

    $calendar = $this->app->getUrlParams()['calendar'];
    $mSharedCalendar = new SharedCalendar();

    SharedCalendar::create([
      'calendar' => $calendar,
      'share_key' => bin2hex(random_bytes(10)),
      'id_owner' => $this->app->auth->getUserId(),
    ]);

    return $mSharedCalendar->get(['calendar', 'share_key'])->toArray();
  }

}