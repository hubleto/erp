<?php

namespace Hubleto\App\Community\Mail\Controllers\Api;

class MarkAsUnread extends \Hubleto\Erp\Controllers\ApiController
{
  public function renderJson(): array
  {
    $idAccount = $this->router()->urlParamAsInteger('idAccount');
    $idMailbox = $this->router()->urlParamAsInteger('idMailbox');
    $idMail = $this->router()->urlParamAsInteger('idMail');

    $mMail = $this->getModel(\Hubleto\App\Community\Mail\Models\Mail::class);

    $mMail->record
      ->where('id', $idMail)
      ->where('id_mailbox', $idMailbox)
      ->update(['datetime_read' => null]);

      return ['success' => true];
  }

}
