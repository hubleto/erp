<?php

namespace Hubleto\App\Community\Notifications\Controllers\Api;

class MarkAsUnread extends \Hubleto\Erp\Controllers\ApiController
{
  public function renderJson(): ?array
  {
    $idNotification = $this->router()->urlParamAsInteger('idNotification');
    $mNotification = $this->getModel(\Hubleto\App\Community\Notifications\Models\Notification::class);
    $mNotification->record->find($idNotification)->update(['datetime_read' => null]);
    return ['success' => true];
  }

}
