<?php

namespace Hubleto\App\Community\Notifications\Controllers;

class All extends \Hubleto\Erp\Controller
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

    $this->setView('@Hubleto:App:Community:Notifications/ListFolder.twig');
  }

}
