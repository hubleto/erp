<?php

namespace HubletoApp\Community\Notifications\Controllers\Api;

class MarkAsRead extends \HubletoMain\Core\Controllers\ApiController
{

  public function renderJson(): ?array
  {
    $idNotification = $this->main->urlParamAsInteger('idNotification');
    $mNotification = new \HubletoApp\Community\Notifications\Models\Notification($this->main);
    $mNotification->record->find($idNotification)->update(['datetime_read' => date('Y-m-d H:i:s')]);
    return ['success' => true];
  }

}