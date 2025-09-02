<?php

namespace Hubleto\App\Community\Mail\Controllers;

use Hubleto\App\Community\Mail\Models\Mailbox;

class Mails extends \Hubleto\Erp\Controller
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

    $idMailbox = $this->router()->urlParamAsInteger('idMailbox');
    $mMailbox = $this->getService(Mailbox::class);

    $this->viewParams['mailbox'] = $mMailbox->record->find($idMailbox)?->toArray();

    $this->setView('@Hubleto:App:Community:Mail/Mails.twig');
  }

}
