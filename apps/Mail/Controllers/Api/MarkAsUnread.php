<?php

namespace HubletoApp\Community\Mail\Controllers\Api;

class MarkAsUnread extends \Hubleto\Erp\Controllers\ApiController
{
  public function renderJson(): ?array
  {
    $idMail = $this->getRouter()->urlParamAsInteger('idMail');
    $mMail = $this->getModel(\HubletoApp\Community\Mail\Models\Mail::class);
    $mMail->record->find($idMail)->update(['datetime_read' => null]);
    return ['success' => true];
  }

}
