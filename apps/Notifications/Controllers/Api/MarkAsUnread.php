<?php

namespace HubletoApp\Community\Notifications\Controllers\Api;

class MarkAsUnread extends \HubletoMain\Controllers\ApiController
{
  public function renderJson(): ?array
  {
    $idNotification = $this->main->urlParamAsInteger('idNotification');
    $mNotification = $this->main->di->create(\HubletoApp\Community\Notifications\Models\Notification::class);
    $mNotification->record->find($idNotification)->update(['datetime_read' => null]);
    return ['success' => true];
  }

}
