<?php

namespace Hubleto\App\Community\EmailMarketing\Controllers;

use Hubleto\App\Community\EmailMarketing\Models\Email;
use Hubleto\App\Community\EmailMarketing\Models\EmailRecipient;
use Hubleto\App\Community\EmailMarketing\Lib;

class EmailPreview extends \Hubleto\Erp\Controller
{
  public bool $hideDefaultDesktop = true;
  public bool $requiresAuthenticatedUser = false;

  public function render(): string
  {
    $urlDataB64 = $this->router()->urlParamAsString('c');

    $urlDataJson = @base64_decode($urlDataB64);
    $urlData = @json_decode($urlDataJson, true);

    $emailUid = $urlData['cuid'] ?? '';
    $idRecipient = (int) ($urlData['rcid'] ?? 0);

    if (empty($emailUid)) return 'Unknown email.';

    /** @var Email */
    $mEmail = $this->getModel(Email::class);

    /** @var EmailRecipient */
    $mRecipient = $this->getModel(EmailRecipient::class);

    $email = $mEmail->record->where('uid', $emailUid)->first();

    $recipient = $mRecipient->record
      ->where('id', $idRecipient)
      ->where('id_email', $email->id)
      ->with('CONTACT.VALUES')
      ->with('MAIL')
      ->first();

    $bodyHtml = Lib::getMailPreview(
      $email->toArray(),
      ($recipient ? $recipient->toArray() : []),
    );

    return $bodyHtml;

  }

}
