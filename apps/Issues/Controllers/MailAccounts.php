<?php

namespace HubletoApp\Community\Issues\Controllers;

class MailAccounts extends \HubletoMain\Controller
{
  public function getBreadcrumbs(): array
  {
    return array_merge(parent::getBreadcrumbs(), [
      [ 'url' => 'issues', 'content' => 'Issues' ],
      [ 'url' => 'mail-accounts', 'content' => 'Mail accounts' ],
    ]);
  }

  public function prepareView(): void
  {
    parent::prepareView();
    $this->setView('@HubletoApp:Community:Issues/MailAccounts.twig');
  }

}
