<?php

namespace Hubleto\App\Community\Issues\Controllers;

class MailAccounts extends \Hubleto\Erp\Controller
{
  public function getBreadcrumbs(): array
  {
    return array_merge(parent::getBreadcrumbs(), [
      [ 'url' => 'issues', 'content' => $this->translate('Issues') ],
      [ 'url' => 'mail-accounts', 'content' => $this->translate('Mail accounts') ],
    ]);
  }

  public function prepareView(): void
  {
    parent::prepareView();
    $this->setView('@Hubleto:App:Community:Issues/MailAccounts.twig');
  }

}
