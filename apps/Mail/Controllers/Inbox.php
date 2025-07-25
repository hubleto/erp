<?php

namespace HubletoApp\Community\Mail\Controllers;

class Inbox extends \HubletoMain\Controller
{
  public function getBreadcrumbs(): array
  {
    return array_merge(parent::getBreadcrumbs(), [
      [ 'url' => 'mail', 'content' => $this->translate('Mail') ],
      [ 'url' => 'inbox', 'content' => $this->translate('Inbox') ],
    ]);
  }

  public function prepareView(): void
  {
    parent::prepareView();

    $this->viewParams['title'] = 'Inbox';
    $this->viewParams['folder'] = 'inbox';

    $this->setView('@HubletoApp:Community:Mail/ListFolder.twig');
  }

}
