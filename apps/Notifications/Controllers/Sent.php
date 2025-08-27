<?php

namespace HubletoApp\Community\Notifications\Controllers;

class Sent extends \HubletoMain\Controller
{
  public function getBreadcrumbs(): array
  {
    return array_merge(parent::getBreadcrumbs(), [
      [ 'url' => 'notifications', 'content' => $this->translate('Notifications') ],
      [ 'url' => 'sent', 'content' => $this->translate('Sent') ],
    ]);
  }

  public function prepareView(): void
  {
    parent::prepareView();

    $this->viewParams['title'] = 'Sent';
    $this->viewParams['folder'] = 'sent';

    $this->setView('@HubletoApp:Community:Notifications/ListFolder.twig');
  }

}
