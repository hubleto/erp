<?php

namespace Hubleto\App\Community\Mail\Controllers\Api;

class MarkAsRead extends \Hubleto\Erp\Controllers\ApiController
{
  public function renderJson(): ?array
  {
    $idMail = $this->getRouter()->urlParamAsInteger('idMail');
    $mMail = $this->getModel(\Hubleto\App\Community\Mail\Models\Mail::class);
    $mMail->record->find($idMail)->update(['datetime_read' => date('Y-m-d H:i:s')]);
    return ['success' => true];
  }

}
