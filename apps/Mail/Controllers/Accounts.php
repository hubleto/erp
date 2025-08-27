<?php

namespace HubletoApp\Community\Mail\Controllers;

class Accounts extends \HubletoMain\Controller
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

    $this->setView('@HubletoApp:Community:Mail/Accounts.twig');
  }

}
