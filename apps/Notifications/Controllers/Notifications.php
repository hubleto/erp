<?php

namespace Hubleto\App\Community\Notifications\Controllers;

class Notifications extends \Hubleto\Erp\Controller
{
  public function getBreadcrumbs(): array
  {
    return array_merge(parent::getBreadcrumbs(), [
      // [ 'url' => 'notifications', 'content' => $this->translate('Notifications') ],
    ]);
  }

  public function prepareView(): void
  {
    parent::prepareView();

    $this->setView('@Hubleto:App:Community:Notifications/Notifications.twig');
  }

}
