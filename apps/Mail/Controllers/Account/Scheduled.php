<?php

namespace Hubleto\App\Community\Mail\Controllers\Account;

use Hubleto\App\Community\Mail\Models\Mailbox;
use Hubleto\App\Community\Mail\Models\Account;

class Scheduled extends \Hubleto\Erp\Controller
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

    $idAccount = $this->router()->urlParamAsInteger("idAccount");

    $mAccount = $this->getModel(Account::class);

    $this->viewParams['account'] = $mAccount->record->prepareReadQuery()->with('MAILBOXES')->where('id', $idAccount)->first();

    $this->setView('@Hubleto:App:Community:Mail/Account/Scheduled.twig');
  }

}
