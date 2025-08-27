<?php

namespace HubletoApp\Community\Mail\Controllers;

use HubletoApp\Community\Mail\Models\Mailbox;

class Drafts extends \HubletoMain\Controller
{
  public function getBreadcrumbs(): array
  {
    return array_merge(parent::getBreadcrumbs(), [
      [ 'url' => 'mail', 'content' => $this->translate('Mail') ],
      [ 'url' => 'drafts', 'content' => $this->translate('Drafts') ],
    ]);
  }

  public function prepareView(): void
  {
    parent::prepareView();

    $this->setView('@HubletoApp:Community:Mail/Drafts.twig');
  }

}
