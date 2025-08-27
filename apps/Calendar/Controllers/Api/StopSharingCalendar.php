<?php

namespace Hubleto\App\Community\Calendar\Controllers\Api;

use Hubleto\Erp\Controller;
use Hubleto\App\Community\Calendar\Models\RecordManagers\SharedCalendar;

class StopSharingCalendar extends \Hubleto\Erp\Controller
{
  public int $returnType = Controller::RETURN_TYPE_JSON;

  public function renderJson(): ?array
  {
    if (!isset($this->getRouter()->getUrlParams()['calendar'])) {
      return [];
    }

    if (isset($this->getRouter()->getUrlParams()['share_key'])) {
      $shareKey = $this->getRouter()->getUrlParams()['share_key'];
    }

    $calendar = $this->getRouter()->getUrlParams()['calendar'];
    $mSharedCalendar = new SharedCalendar();

    $calendar = $mSharedCalendar->where('calendar', $calendar);
    if (isset($this->getRouter()->getUrlParams()['share_key'])) {
      $calendar = $calendar->where('share_key', $this->getRouter()->getUrlParams()['share_key']);
    }
    $calendar->delete();
    return $mSharedCalendar->get('calendar', 'share_key')->toArray();
  }

}
