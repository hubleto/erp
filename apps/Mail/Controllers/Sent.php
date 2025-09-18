<?php

namespace Hubleto\App\Community\Mail\Controllers;

class Sent extends \Hubleto\Erp\Controller
{
  public function getBreadcrumbs(): array
  {
    return array_merge(parent::getBreadcrumbs(), [
      [ 'url' => 'sent', 'content' => $this->translate('Sent') ],
    ]);
  }

  public function prepareView(): void
  {
    parent::prepareView();

    $this->setView('@Hubleto:App:Community:Mail/Sent.twig');
  }

}
