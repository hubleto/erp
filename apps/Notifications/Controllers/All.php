<?php

namespace HubletoApp\Community\Notifications\Controllers;

class All extends \HubletoMain\Controller
{
  public function getBreadcrumbs(): array
  {
    return array_merge(parent::getBreadcrumbs(), [
      [ 'url' => 'notifications', 'content' => $this->translate('Notifications') ],
      [ 'url' => 'all', 'content' => $this->translate('All') ],
    ]);
  }

  public function prepareView(): void
  {
    parent::prepareView();

    $this->viewParams['title'] = 'All';
    $this->viewParams['folder'] = 'all';

    $this->setView('@HubletoApp:Community:Notifications/ListFolder.twig');
  }

}
