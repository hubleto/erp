<?php declare(strict_types=1);

namespace Hubleto\App\Community\Issues\EventListeners;

use Hubleto\App\Community\Issues\Models\Issue;
use Hubleto\App\Community\Issues\Models\Post;
use Hubleto\Erp\Controller;

class CreateIssueFromMail extends \Hubleto\Framework\EventListener implements \Hubleto\Framework\Interfaces\EventListenerInterface
{

  public function onMailReceived(array $mail, array $attachments): void
  {
    $inReplyTo = $mail['in_reply_to'] ?? '';
    $idIssue = 0;

    /** @var Issue */
    $mIssue = $this->getModel(Issue::class);

    /** @var Post */
    $mPost = $this->getModel(Post::class);

    if (!empty($inReplyTo)) {
      $issue = $mIssue->record->where('thread_uid', $inReplyTo)->first();
      $idIssue = $issue->id ?? 0;
    }

    if ($idIssue == 0) {
      $idIssue = $mIssue->record->recordCreate([
        'title' => $mail['subject'],
        'description' => $mail['body_text'],
        'from' => $mail['from'],
        'thread_uid' => $mail['in_reply_to'],
      ])['id'];
    }

    if ($idIssue > 0) {
      $mPost->record->recordCreate([
        'id_issue' => $idIssue,
        'content' => $mail['body_text'],
        'id_mail' => $mail['id'],
        'thread_uid' => $mail['in_reply_to'],
      ]);
    }
  }

}