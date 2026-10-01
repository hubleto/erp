<?php

namespace Hubleto\App\Community\LinkedinMessages;

use Hubleto\Erp\Core;

class Counter extends Core
{

  /**
   * Query for incoming messages that have not been read yet.
   *
   * @return mixed
   */
  public function queryForUnreadMessages(): mixed
  {
    $mMessage = $this->getModel(Models\Message::class);

    return $mMessage->record->prepareReadQuery()
      ->where($mMessage->table . '.is_read', false)
      ->where($mMessage->table . '.direction', Models\Message::DIRECTION_INCOMING)
    ;
  }

  public function unreadMessages(): int
  {
    return $this->queryForUnreadMessages()->count();
  }

  /**
   * Query for incoming messages that have no planned follow-up in the future.
   *
   * @return mixed
   */
  public function queryForMessagesWithoutFollowup(): mixed
  {
    $mMessage = $this->getModel(Models\Message::class);

    return $mMessage->record->prepareReadQuery()
      ->where($mMessage->table . '.direction', Models\Message::DIRECTION_INCOMING)
      ->whereDoesntHave('ACTIVITIES', function ($q) {
        $q->where('completed', false);
        $q->whereDate('date_start', '>=', date('Y-m-d'));
      })
    ;
  }

  public function messagesWithoutFollowup(): int
  {
    return $this->queryForMessagesWithoutFollowup()->count();
  }

}
