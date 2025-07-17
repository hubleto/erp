<?php

namespace HubletoApp\Community\Calendar\Controllers\Api;


use ADIOS\Core\Controller;
use HubletoApp\Community\Calendar\Models\RecordManagers\SharedCalendar;

class StopSharingCalendar extends \HubletoMain\Core\Controllers\Controller
{

  public int $returnType = Controller::RETURN_TYPE_JSON;

  public function renderJson(): ?array
  {
    if (!isset($this->app->getUrlParams()['calendar'])) {
      return [];
    }

    if (isset($this->app->getUrlParams()['share_key'])) {
      $shareKey = $this->app->getUrlParams()['share_key'];
    }

    $calendar = $this->app->getUrlParams()['calendar'];
    $mSharedCalendar = new SharedCalendar();

    $calendar = $mSharedCalendar->where('calendar', $calendar);
    if (isset($this->app->getUrlParams()['share_key'])) {
      $calendar = $calendar->where('share_key', $this->app->getUrlParams()['share_key']);
    }
    $calendar->delete();
    return $mSharedCalendar->get('calendar', 'share_key')->toArray();
  }

}