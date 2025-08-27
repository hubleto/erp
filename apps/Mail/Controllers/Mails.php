<?php

namespace HubletoApp\Community\Mail\Controllers;

use HubletoApp\Community\Mail\Models\Mailbox;

class Mails extends \HubletoMain\Controller
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

    $idMailbox = $this->getRouter()->urlParamAsInteger('idMailbox');
    $mMailbox = $this->getService(Mailbox::class);

    $this->viewParams['mailbox'] = $mMailbox->record->find($idMailbox)?->toArray();

    $this->setView('@HubletoApp:Community:Mail/Mails.twig');
  }

}
