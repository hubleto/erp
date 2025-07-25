<?php

namespace HubletoApp\Community\Mail\Controllers;

class Sent extends \HubletoMain\Controller
{
  public function getBreadcrumbs(): array
  {
    return array_merge(parent::getBreadcrumbs(), [
      [ 'url' => 'mail', 'content' => $this->translate('Mail') ],
      [ 'url' => 'sent', 'content' => $this->translate('Sent') ],
    ]);
  }

  public function prepareView(): void
  {
    parent::prepareView();

    $this->viewParams['title'] = 'Sent';
    $this->viewParams['folder'] = 'sent';

    $this->setView('@HubletoApp:Community:Mail/ListFolder.twig');
  }

}
