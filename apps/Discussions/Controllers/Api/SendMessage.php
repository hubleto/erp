<?php

namespace HubletoApp\Community\Discussions\Controllers\Api;

class SendMessage extends \Hubleto\Erp\Controllers\ApiController
{
  public function response(): array
  {
    $idDiscussion = $this->getRouter()->urlParamAsInteger('idDiscussion');
    $message = $this->getRouter()->urlParamAsString('message');

    $idUser = $this->getAuthProvider()->getUserId();

    $mMessage = $this->getModel(\HubletoApp\Community\Discussions\Models\Message::class);
    $mMember = $this->getModel(\HubletoApp\Community\Discussions\Models\Member::class);

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
