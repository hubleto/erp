<?php

namespace Hubleto\App\Community\LinkedinMessages\Controllers;

class Messages extends \Hubleto\Erp\Controller
{
  public function getBreadcrumbs(): array
  {
    return array_merge(parent::getBreadcrumbs(), [
      [ 'url' => '', 'content' => $this->translate('Messages') ],
    ]);
  }

  public function prepareView(): void
  {
    parent::prepareView();
    $this->setView('@Hubleto:App:Custom:LinkedinMessages/Messages.twig');
  }
}
