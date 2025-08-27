<?php

namespace HubletoApp\Community\Notifications\Controllers\Api;

class MarkAsRead extends \HubletoMain\Controllers\ApiController
{
  public function renderJson(): ?array
  {
    $idNotification = $this->getRouter()->urlParamAsInteger('idNotification');
    $mNotification = $this->getModel(\HubletoApp\Community\Notifications\Models\Notification::class);
    $mNotification->record->find($idNotification)->update(['datetime_read' => date('Y-m-d H:i:s')]);
    return ['success' => true];
  }

}
