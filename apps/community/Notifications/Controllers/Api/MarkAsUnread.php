<?php

namespace HubletoApp\Community\Notifications\Controllers\Api;

class MarkAsUnread extends \HubletoMain\Core\Controllers\ApiController
{

  public function renderJson(): ?array
  {
    $idNotification = $this->main->urlParamAsInteger('idNotification');
    $mNotification = new \HubletoApp\Community\Notifications\Models\Notification($this->main);
    $mNotification->record->find($idNotification)->update(['datetime_read' => null]);
    return ['success' => true];
  }

}