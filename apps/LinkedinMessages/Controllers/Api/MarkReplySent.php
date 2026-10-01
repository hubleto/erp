<?php

namespace Hubleto\App\Community\LinkedinMessages\Controllers\Api;

use Hubleto\App\Community\LinkedinMessages\Sync;
use Hubleto\App\Community\LinkedinMessages\Models\Message;
use Hubleto\App\Community\LinkedinMessages\Models\Reply;

class MarkReplySent extends \Hubleto\Erp\Controllers\ApiController
{
  public function renderJson(): array
  {
    $idReply = $this->router()->urlParamAsInteger('idReply');

    /** @var Reply */
    $mReply = $this->getModel(Reply::class);
    /** @var Message */
    $mMessage = $this->getModel(Message::class);
    /** @var Sync */
    $sync = $this->getService(Sync::class);

    $reply = $mReply->record->prepareReadQuery()->where($mReply->table . '.id', $idReply)->first()?->toArray();
    if (!$reply) {
      return [ 'status' => 'error', 'message' => $this->translate('Reply not found.') ];
    }

    if (in_array($reply['status'], [Reply::STATUS_MANUAL, Reply::STATUS_FAILED])) {
      $mReply->record->where('id', $idReply)->update([
        'status' => Reply::STATUS_MARKED_SENT,
        'sent_at' => date('Y-m-d H:i:s'),
      ]);

      $message = $mMessage->record->where('id', $reply['id_message'])->first()?->toArray();
      if ($message) {
        $sync->storeOutgoingMessage($message, (string) $reply['body']);
      }
    }

    return [ 'status' => 'success', 'idReply' => $idReply ];
  }

}
