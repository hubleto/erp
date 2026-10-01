<?php

namespace Hubleto\App\Custom\LinkedinMessages\Controllers\Api;

use Hubleto\App\Custom\LinkedinMessages\Models\Message;
use Hubleto\App\Custom\LinkedinMessages\Models\MessageActivity;

class LogActivity extends \Hubleto\Erp\Controllers\ApiController
{
  public function renderJson(): array
  {
    $idMessage = $this->router()->urlParamAsInteger('idMessage');
    $activity = $this->router()->urlParamAsString('activity');

    if ($idMessage > 0 && $activity != '') {
      /** @var Message */
      $mMessage = $this->getModel(Message::class);
      $message = $mMessage->record->prepareReadQuery()->where($mMessage->table . '.id', $idMessage)->first()?->toArray();

      if ($message && $message['id'] > 0) {
        $mMessageActivity = $this->getModel(MessageActivity::class);
        $mMessageActivity->record->recordCreate([
          'id_message' => $idMessage,
          'subject' => $activity,
          'date_start' => date('Y-m-d'),
          'time_start' => date('H:i:s'),
          'all_day' => true,
          'completed' => true,
          'id_owner' => $this->authProvider()->getUserId(),
        ]);
      }
    }

    return [
      'status' => 'success',
      'idMessage' => $idMessage,
    ];
  }

}
