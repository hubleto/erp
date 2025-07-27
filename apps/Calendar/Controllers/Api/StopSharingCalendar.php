<?php

namespace HubletoApp\Community\Calendar\Controllers\Api;

use HubletoMain\Controller;
use HubletoApp\Community\Calendar\Models\RecordManagers\SharedCalendar;

class StopSharingCalendar extends \HubletoMain\Controller
{
  public int $returnType = Controller::RETURN_TYPE_JSON;

  public function renderJson(): ?array
  {
    if (!isset($this->main->getUrlParams()['calendar'])) {
      return [];
    }

    if (isset($this->main->getUrlParams()['share_key'])) {
      $shareKey = $this->main->getUrlParams()['share_key'];
    }

    $calendar = $this->main->getUrlParams()['calendar'];
    $mSharedCalendar = new SharedCalendar();

    $calendar = $mSharedCalendar->where('calendar', $calendar);
    if (isset($this->main->getUrlParams()['share_key'])) {
      $calendar = $calendar->where('share_key', $this->main->getUrlParams()['share_key']);
    }
    $calendar->delete();
    return $mSharedCalendar->get('calendar', 'share_key')->toArray();
  }

}
