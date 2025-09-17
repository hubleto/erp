<?php

namespace Hubleto\App\Community\Mail\Controllers;

use Hubleto\App\Community\Mail\Models\Mailbox;
use Hubleto\App\Community\Mail\Models\Account;

class Mailboxes extends \Hubleto\Erp\Controller
{
  public function getBreadcrumbs(): array
  {
    return array_merge(parent::getBreadcrumbs(), [
      [ 'url' => 'mail', 'content' => $this->translate('Mail') ],
    ]);
  }

  public function prepareView(): void
  {
    parent::prepareView();

    $mMailbox = $this->getModel(Mailbox::class);
    $mAccount = $this->getModel(Account::class);

    $this->viewParams['accounts'] = $mAccount->record->prepareReadQuery()->get()->toArray();
    $this->viewParams['mailboxes'] = $mMailbox->record->prepareReadQuery()->get()->toArray();

    $this->setView('@Hubleto:App:Community:Mail/Mailboxes.twig');
  }

}
