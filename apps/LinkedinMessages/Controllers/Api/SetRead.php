<?php

namespace Hubleto\App\Community\LinkedinMessages\Controllers\Api;

use Hubleto\App\Community\LinkedinMessages\Models\Message;

class SetRead extends \Hubleto\Erp\Controllers\ApiController
{
  public function renderJson(): array
  {
    $idMessage = $this->router()->urlParamAsInteger('idMessage');
    $read = $this->router()->urlParamAsBool('read', true);

    /** @var Message */
    $mMessage = $this->getModel(Message::class);

    $message = $mMessage->record->prepareReadQuery()->where($mMessage->table . '.id', $idMessage)->first()?->toArray();
    if (!$message) {
      return [ 'status' => 'error', 'message' => $this->translate('Message not found.') ];
    }

    $mMessage->record->where('id', $idMessage)->update([ 'is_read' => $read ? 1 : 0 ]);

    return [ 'status' => 'success', 'idMessage' => $idMessage, 'read' => $read ];
  }

}
