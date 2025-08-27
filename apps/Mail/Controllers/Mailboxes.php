<?php

namespace HubletoApp\Community\Mail\Controllers;

use HubletoApp\Community\Mail\Models\Mailbox;

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

    $mMailbox = $this->getService(Mailbox::class);

    $this->viewParams['mailboxes'] = $mMailbox->record->prepareReadQuery()->get()->toArray();

    $this->setView('@HubletoApp:Community:Mail/Mailboxes.twig');
  }

}
