<?php

namespace Hubleto\App\Community\Mail\Controllers;

use Hubleto\App\Community\Mail\Models\Mailbox;
use Hubleto\App\Community\Mail\Models\Account;

class Home extends \Hubleto\Erp\Controller
{
  public function getBreadcrumbs(): array
  {
    return array_merge(parent::getBreadcrumbs(), [
      [ 'url' => 'mail', 'content' => $this->translate('Mail') ],
      [ 'url' => 'accounts', 'content' => $this->translate('Accounts') ],
    ]);
  }

  public function prepareView(): void
  {
    parent::prepareView();

    $mAccount = $this->getModel(Account::class);

    $this->viewParams['accounts'] = $mAccount->record->prepareReadQuery()->with('MAILBOXES')->get();

    $this->setView('@Hubleto:App:Community:Mail/Home.twig');
  }

}
