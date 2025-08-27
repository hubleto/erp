<?php

namespace HubletoApp\Community\Notifications\Controllers;

class Inbox extends \HubletoMain\Controller
{
  public function getBreadcrumbs(): array
  {
    return array_merge(parent::getBreadcrumbs(), [
      [ 'url' => 'notifications', 'content' => $this->translate('Notifications') ],
      [ 'url' => 'inbox', 'content' => $this->translate('Inbox') ],
    ]);
  }

  public function prepareView(): void
  {
    parent::prepareView();

    $this->viewParams['title'] = 'Inbox';
    $this->viewParams['folder'] = 'inbox';

    $this->setView('@HubletoApp:Community:Notifications/ListFolder.twig');
  }

}
