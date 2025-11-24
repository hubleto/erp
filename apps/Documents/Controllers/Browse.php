<?php

namespace Hubleto\App\Community\Documents\Controllers;

class Browse extends \Hubleto\Erp\Controller
{
  public function getBreadcrumbs(): array
  {
    return array_merge(parent::getBreadcrumbs(), [
      [ 'url' => 'documents/browse', 'content' => $this->translate('Document Browser') ],
    ]);
  }

  public function prepareView(): void
  {
    parent::prepareView();
    $this->setView('@Hubleto:App:Community:Documents/Browse.twig');
  }

}
