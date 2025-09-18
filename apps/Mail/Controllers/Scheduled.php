<?php

namespace Hubleto\App\Community\Mail\Controllers;

class Scheduled extends \Hubleto\Erp\Controller
{
  public function getBreadcrumbs(): array
  {
    return array_merge(parent::getBreadcrumbs(), [
      [ 'url' => 'scheduled', 'content' => $this->translate('Scheduled') ],
    ]);
  }

  public function prepareView(): void
  {
    parent::prepareView();

    $this->setView('@Hubleto:App:Community:Mail/Scheduled.twig');
  }

}
