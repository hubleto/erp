<?php

namespace Hubleto\App\Community\EmailMarketing\Controllers;

use Hubleto\App\Community\EmailMarketing\Models\Email;
use Hubleto\App\Community\EmailMarketing\Models\RecipientStatus;

class Unsubscribe extends \Hubleto\Erp\Controller
{
  public bool $hideDefaultDesktop = true;
  public bool $requiresAuthenticatedUser = false;

  public function prepareView(): void
  {
    $urlDataB64 = $this->router()->urlParamAsString('c');
    $recipientEmail = $this->router()->urlParamAsString('recipientEmail');

    $urlDataJson = @base64_decode($urlDataB64);
    $urlData = @json_decode($urlDataJson, true);

    $emailUid = $urlData['cuid'] ?? '';

    /** @var Email */
    $mEmail = $this->getModel(Email::class);

    /** @var RecipientStatus */
    $mRecipientStatus = $this->getModel(RecipientStatus::class);

    $email = $mEmail->record->where('uid', $emailUid)->first();

    if (!empty($email) && $email) {
      $mRecipientStatus->record->create([
        'email' => $recipientEmail,
        'is_unsubscribed' => true,
      ]);
    }

    $this->viewParams['email'] = $email;
    $this->viewParams['recipientEmail'] = $recipientEmail;
    $this->viewParams['c'] = $urlDataB64;

    $this->setView('@Hubleto:App:Community:Campaigns/Unsubscribe.twig');

  }

}
