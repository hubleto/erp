<?php

namespace Hubleto\App\Community\LinkedinMessages\Controllers\Api;

use Hubleto\App\Community\LinkedinMessages\Client;
use Hubleto\App\Community\LinkedinMessages\Sync;
use Hubleto\App\Community\LinkedinMessages\Models\Account;
use Hubleto\App\Community\LinkedinMessages\Models\Message;
use Hubleto\App\Community\LinkedinMessages\Models\Profile;
use Hubleto\App\Community\LinkedinMessages\Models\Reply;

class SendReply extends \Hubleto\Erp\Controllers\ApiController
{
  public function renderJson(): array
  {
    $idMessage = $this->router()->urlParamAsInteger('idMessage');
    $body = trim($this->router()->urlParamAsString('body'));

    /** @var Message */
    $mMessage = $this->getModel(Message::class);
    /** @var Reply */
    $mReply = $this->getModel(Reply::class);
    /** @var Account */
    $mAccount = $this->getModel(Account::class);
    /** @var Profile */
    $mProfile = $this->getModel(Profile::class);
    /** @var Client */
    $client = $this->getService(Client::class);
    /** @var Sync */
    $sync = $this->getService(Sync::class);

    if ($body === '') {
      return [ 'status' => 'error', 'message' => $this->translate('Write the reply first.') ];
    }

    $message = $mMessage->record->prepareReadQuery()->where($mMessage->table . '.id', $idMessage)->first()?->toArray();
    if (!$message) {
      return [ 'status' => 'error', 'message' => $this->translate('Message not found.') ];
    }

    $account = !empty($message['id_account']) ? $mAccount->record->where('id', $message['id_account'])->first()?->toArray() : null;
    $profile = !empty($message['id_profile']) ? $mProfile->record->where('id', $message['id_profile'])->first()?->toArray() : null;

    $replyStatus = Reply::STATUS_MANUAL;
    $errorInfo = '';
    $sentAt = null;

    $recipientUrn = (string) ($profile['linkedin_urn'] ?? '');
    $canTryApi = $account
      && !empty($account['access_token'])
      && str_contains((string) ($account['scopes'] ?? ''), 'w_messages')
      && str_starts_with($recipientUrn, 'urn:li:person:');

    if (!$account || empty($account['access_token'])) {
      $errorInfo = $this->translate('The account is not connected to LinkedIn. Send the reply manually.');
    } elseif (!$canTryApi) {
      $errorInfo = $this->translate('Sending through the LinkedIn API is not available for this account (it needs partner access with the w_messages scope). Send the reply manually.');
    } else {
      $threadUrn = str_starts_with((string) ($message['thread_id'] ?? ''), 'urn:li:messagingThread:') ? $message['thread_id'] : '';
      $response = $client->sendMessage($account['access_token'], [ $recipientUrn ], (string) ($message['subject'] ?? ''), $body, $threadUrn);

      if ($response['status'] >= 200 && $response['status'] < 300) {
        $replyStatus = Reply::STATUS_SENT;
        $sentAt = date('Y-m-d H:i:s');
      } elseif (in_array($response['status'], [401, 403, 404])) {
        $errorInfo = $this->translate('LinkedIn refused the request, this app has no permission to send messages.') . ' ' . $client->describeError($response);
      } else {
        $replyStatus = Reply::STATUS_FAILED;
        $errorInfo = $this->translate('Sending failed.') . ' ' . $client->describeError($response);
      }
    }

    $reply = $mReply->record->recordCreate([
      'id_message' => $idMessage,
      'id_owner' => $this->authProvider()->getUserId(),
      'body' => $body,
      'status' => $replyStatus,
      'error_info' => $errorInfo,
      'sent_at' => $sentAt,
      'date_created' => date('Y-m-d H:i:s'),
    ]);

    // answering a message means that it was read
    $mMessage->record->where('id', $idMessage)->update([ 'is_read' => 1 ]);

    if ($replyStatus == Reply::STATUS_SENT) {
      $sync->storeOutgoingMessage($message, $body);
    }

    $hasFutureFollowup = $mMessage->record
      ->where('id', $idMessage)
      ->whereHas('ACTIVITIES', function ($q) {
        $q->where('completed', false);
        $q->whereDate('date_start', '>=', date('Y-m-d'));
      })
      ->exists()
    ;

    $result = match ($replyStatus) {
      Reply::STATUS_SENT => 'sent',
      Reply::STATUS_FAILED => 'failed',
      default => 'manual',
    };

    $messages = [
      'sent' => $this->translate('Reply was sent to LinkedIn.'),
      'manual' => $this->translate('Reply was saved. Copy it and send it on LinkedIn, then confirm it here.'),
      'failed' => $this->translate('Reply could not be sent. It was saved so you can send it manually.'),
    ];

    return [
      'status' => 'success',
      'result' => $result,
      'idReply' => $reply['id'],
      'message' => $messages[$result],
      'details' => $errorInfo,
      'conversationUrl' => 'https://www.linkedin.com/messaging/',
      'profileUrl' => (string) ($profile['profile_url'] ?? ''),
      // the user shall be asked to plan a follow-up after every reply
      'promptFollowup' => true,
      'hasFutureFollowup' => $hasFutureFollowup,
    ];
  }

}
