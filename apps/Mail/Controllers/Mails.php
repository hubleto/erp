<?php

namespace Hubleto\App\Community\Mail\Controllers;

use Hubleto\App\Community\Mail\Models\Mailbox;
use Hubleto\App\Community\Mail\Models\Account;

class Mails extends \Hubleto\Erp\Controller
{
  public function getBreadcrumbs(): array
  {
    return array_merge(parent::getBreadcrumbs(), [
      // [ 'url' => 'mail', 'content' => $this->translate('Mail') ],
    ]);
  }

  public function prepareView(): void
  {
    parent::prepareView();

    $mAccount = $this->getModel(Account::class);
    $mMailbox = $this->getModel(Mailbox::class);

    $idMailbox = $this->router()->urlParamAsInteger("idMailbox");
    $mailbox = $mMailbox->record->prepareReadQuery()->where('mails_mailboxes.id', $idMailbox)->first();

    $idAccount = (int) ($mailbox['id_account'] ?? 0);
    $account = $mAccount->record->prepareReadQuery()->with('MAILBOXES')->where('mails_accounts.id', $idAccount)->first();

    $this->viewParams['account'] = $account;
    $this->viewParams['mailbox'] = $mailbox;
    $this->viewParams['idAccount'] = $idAccount;
    $this->viewParams['idMailbox'] = $idMailbox;

    $this->setView('@Hubleto:App:Community:Mail/Mails.twig');
  }

}
