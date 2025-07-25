<?php

namespace HubletoApp\Community\Mail\Controllers;

class Outbox extends \HubletoMain\Controller
{
  public function getBreadcrumbs(): array
  {
    return array_merge(parent::getBreadcrumbs(), [
      [ 'url' => 'mail', 'content' => $this->translate('Mail') ],
      [ 'url' => 'outbox', 'content' => $this->translate('Outbox') ],
    ]);
  }

  public function prepareView(): void
  {
    parent::prepareView();

    $this->viewParams['title'] = 'Outbox';
    $this->viewParams['folder'] = 'outbox';

    $this->setView('@HubletoApp:Community:Mail/ListFolder.twig');
  }

}
