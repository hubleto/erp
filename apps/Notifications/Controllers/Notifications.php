<?php

namespace HubletoApp\Community\Notifications\Controllers;

class Notifications extends \Hubleto\Framework\Controllers\Controller
{
  public function getBreadcrumbs(): array
  {
    return array_merge(parent::getBreadcrumbs(), [
      [ 'url' => 'notifications', 'content' => $this->translate('Notifications') ],
    ]);
  }

  public function prepareView(): void
  {
    parent::prepareView();

    $this->setView('@HubletoApp:Community:Notifications/Notifications.twig');
  }

}
