<?php

namespace Hubleto\App\Community\Mail\Controllers\Api;

class MarkAsUnread extends \Hubleto\Erp\Controllers\ApiController
{
  public function renderJson(): array
  {
    $idMail = $this->router()->urlParamAsInteger('idMail');
    $mMail = $this->getModel(\Hubleto\App\Community\Mail\Models\Mail::class);
    $mMail->record->find($idMail)->update(['datetime_read' => null]);
    return ['success' => true];
  }

}
