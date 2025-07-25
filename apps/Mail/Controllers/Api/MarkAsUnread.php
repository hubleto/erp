<?php

namespace HubletoApp\Community\Mail\Controllers\Api;

class MarkAsUnread extends \HubletoMain\Controllers\ApiController
{
  public function renderJson(): ?array
  {
    $idMail = $this->main->urlParamAsInteger('idMail');
    $mMail = $this->main->di->create(\HubletoApp\Community\Mail\Models\Mail::class);
    $mMail->record->find($idMail)->update(['datetime_read' => null]);
    return ['success' => true];
  }

}
