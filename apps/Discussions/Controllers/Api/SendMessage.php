<?php

namespace HubletoApp\Community\Discussions\Controllers\Api;

class SendMessage extends \HubletoMain\Controllers\ApiController
{
  public function response(): array
  {
    $idDiscussion = $this->main->urlParamAsInteger('idDiscussion');
    $message = $this->main->urlParamAsString('message');

    $idUser = $this->main->auth->getUserId();

    $mMessage = $this->main->di->create(\HubletoApp\Community\Discussions\Models\Message::class);
    $mMember = $this->main->di->create(\HubletoApp\Community\Discussions\Models\Member::class);

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
