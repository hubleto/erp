<?php

namespace Hubleto\App\Community\Discussions\Controllers\Api;

use Hubleto\App\Community\Auth\AuthProvider;

class SendMessage extends \Hubleto\Erp\Controllers\ApiController
{
  public function response(): array
  {
    $idDiscussion = $this->router()->urlParamAsInteger('idDiscussion');
    $message = $this->router()->urlParamAsString('message');

    $idUser = $this->getService(AuthProvider::class)->getUserId();

    $mMessage = $this->getModel(\Hubleto\App\Community\Discussions\Models\Message::class);
    $mMember = $this->getModel(\Hubleto\App\Community\Discussions\Models\Member::class);

    $sentMessage = [
      'id_discussion' => $idDiscussion,
      'id_from' => $idUser,
      'message' => $message,
      'sent' => date('Y-m-d H:i:s'),
    ];
    $mMessage->record->recordCreate($sentMessage);

    $member = $mMember->record->where('id_discussion', $idDiscussion)->where('id_member', $idUser)->first()?->toArray();
    if (!isset($member['id'])) {
      $mMember->record->recordCreate(['id_discussion' => $idDiscussion, 'id_member' => $idUser]);
    }


    return [
      "status" => "success",
      "sentMessage" => $sentMessage,
    ];
  }
}
